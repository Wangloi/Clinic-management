<?php
/**
 * Fixed Health Questionnaire Backend Handler
 * Adapted to work with actual database structure
 */

require_once 'connection.php';

class HealthBackendHandlerFixed {
    private $conn;
    private $tableStructure = [];
    
    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
        $this->analyzeTableStructure();
    }
    
    /**
     * Analyze actual database structure
     */
    private function analyzeTableStructure() {
        try {
            // Check students table structure
            $stmt = $this->conn->query("DESCRIBE students");
            $this->tableStructure['students'] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
            
            // Check if other tables exist
            $tables = ['sections', 'programs', 'departments'];
            foreach ($tables as $table) {
                $stmt = $this->conn->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $this->conn->query("DESCRIBE $table");
                    $this->tableStructure[$table] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
                }
            }
        } catch (Exception $e) {
            error_log("Error analyzing table structure: " . $e->getMessage());
        }
    }
    
    /**
     * Get student information using actual database structure
     */
    public function getStudentInfo($studentId) {
        try {
            // Build base query for students table
            $studentColumns = $this->tableStructure['students'] ?? [];
            
            // Build name concatenation based on available columns
            $nameColumns = [];
            if (in_array('Student_Fname', $studentColumns)) $nameColumns[] = 's.Student_Fname';
            if (in_array('Student_Mname', $studentColumns)) $nameColumns[] = 'COALESCE(s.Student_Mname, \'\')';
            if (in_array('Student_Lname', $studentColumns)) $nameColumns[] = 's.Student_Lname';
            
            $nameConcat = !empty($nameColumns) ? 
                "CONCAT(" . implode(", ' ', ", $nameColumns) . ") as full_name" : 
                "'Unknown' as full_name";
            
            $sql = "SELECT s.*, $nameConcat FROM students s WHERE s.Student_ID = :student_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':student_id' => $studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                return false;
            }
            
            // Get section information if possible
            $student['section_name'] = $this->getSectionName($student);
            
            // Get program information if possible  
            $student['program_name'] = $this->getProgramName($student);
            
            // Get department information if possible
            $deptInfo = $this->getDepartmentInfo($student);
            $student['department_name'] = $deptInfo['name'];
            $student['department_level'] = $deptInfo['level'];
            
            return $student;
            
        } catch (Exception $e) {
            error_log("Error in getStudentInfo: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get section name
     */
    private function getSectionName($student) {
        if (!isset($this->tableStructure['sections'])) {
            return 'Unknown';
        }
        
        $sectionColumns = $this->tableStructure['sections'];
        $sectionId = null;
        
        // Find section ID field
        if (isset($student['section_id'])) {
            $sectionId = $student['section_id'];
        } elseif (isset($student['Section_ID'])) {
            $sectionId = $student['Section_ID'];
        }
        
        if (!$sectionId) {
            return 'Unknown';
        }
        
        try {
            // Find section name field
            $nameField = 'section_name';
            if (in_array('Section_Name', $sectionColumns)) {
                $nameField = 'Section_Name';
            } elseif (in_array('name', $sectionColumns)) {
                $nameField = 'name';
            }
            
            // Find section ID field in sections table
            $idField = 'section_id';
            if (in_array('Section_ID', $sectionColumns)) {
                $idField = 'Section_ID';
            } elseif (in_array('id', $sectionColumns)) {
                $idField = 'id';
            }
            
            $sql = "SELECT $nameField FROM sections WHERE $idField = :section_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':section_id' => $sectionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result[$nameField] : 'Unknown';
            
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Get program name based on section
     */
    private function getProgramName($student) {
        // First try to get program through section relationship
        $sectionId = $student['section_id'] ?? $student['Section_ID'] ?? null;
        
        if ($sectionId && isset($this->tableStructure['sections']) && isset($this->tableStructure['programs'])) {
            try {
                $sectionColumns = $this->tableStructure['sections'];
                $programColumns = $this->tableStructure['programs'];
                
                // Find program name field in programs table
                $programNameField = 'program_name';
                if (in_array('Program_Name', $programColumns)) {
                    $programNameField = 'Program_Name';
                } elseif (in_array('name', $programColumns)) {
                    $programNameField = 'name';
                }
                
                // Try different possible relationships between sections and programs
                $possibleQueries = [
                    // Direct relationship: sections.program_id -> programs.program_id
                    "SELECT p.$programNameField FROM programs p 
                     JOIN sections s ON p.program_id = s.program_id 
                     WHERE s.section_id = :section_id",
                     
                    // Alternative column names
                    "SELECT p.$programNameField FROM programs p 
                     JOIN sections s ON p.Program_ID = s.Program_ID 
                     WHERE s.Section_ID = :section_id",
                     
                    // Another variation
                    "SELECT p.$programNameField FROM programs p 
                     JOIN sections s ON p.id = s.program_id 
                     WHERE s.section_id = :section_id"
                ];
                
                foreach ($possibleQueries as $query) {
                    try {
                        $stmt = $this->conn->prepare($query);
                        $stmt->execute([':section_id' => $sectionId]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result && !empty($result[$programNameField])) {
                            return $result[$programNameField];
                        }
                    } catch (Exception $e) {
                        // Try next query
                        continue;
                    }
                }
            } catch (Exception $e) {
                // Fall back to direct program lookup
            }
        }
        
        // Fallback: try direct program lookup if section-based lookup fails
        if (!isset($this->tableStructure['programs'])) {
            return 'Unknown';
        }
        
        $programColumns = $this->tableStructure['programs'];
        $programId = $student['program_id'] ?? $student['Program_ID'] ?? null;
        
        if (!$programId) {
            return 'Unknown';
        }
        
        try {
            // Find program name field
            $nameField = 'program_name';
            if (in_array('Program_Name', $programColumns)) {
                $nameField = 'Program_Name';
            } elseif (in_array('name', $programColumns)) {
                $nameField = 'name';
            }
            
            // Find program ID field in programs table
            $idField = 'program_id';
            if (in_array('Program_ID', $programColumns)) {
                $idField = 'Program_ID';
            } elseif (in_array('id', $programColumns)) {
                $idField = 'id';
            }
            
            $sql = "SELECT $nameField FROM programs WHERE $idField = :program_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':program_id' => $programId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result[$nameField] : 'Unknown';
            
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Get department information based on section → program → department relationship
     */
    private function getDepartmentInfo($student) {
        $defaultDept = ['name' => 'Unknown', 'level' => 'College'];
        
        // First try to get department through section → program → department chain
        $sectionId = $student['section_id'] ?? $student['Section_ID'] ?? null;
        
        if ($sectionId && isset($this->tableStructure['sections']) && 
            isset($this->tableStructure['programs']) && isset($this->tableStructure['departments'])) {
            
            try {
                $deptColumns = $this->tableStructure['departments'];
                
                // Find department name field
                $nameField = 'department_name';
                if (in_array('Department_Name', $deptColumns)) {
                    $nameField = 'Department_Name';
                } elseif (in_array('dept_name', $deptColumns)) {
                    $nameField = 'dept_name';
                } elseif (in_array('name', $deptColumns)) {
                    $nameField = 'name';
                }
                
                // Find department level field
                $levelField = 'department_level';
                if (in_array('Department_Level', $deptColumns)) {
                    $levelField = 'Department_Level';
                } elseif (in_array('level', $deptColumns)) {
                    $levelField = 'level';
                }
                
                // Try different possible relationships: section → program → department
                $possibleQueries = [
                    // Standard relationship
                    "SELECT d.$nameField as dept_name, d.$levelField as dept_level 
                     FROM departments d 
                     JOIN programs p ON d.department_id = p.department_id 
                     JOIN sections s ON p.program_id = s.program_id 
                     WHERE s.section_id = :section_id",
                     
                    // Alternative column names
                    "SELECT d.$nameField as dept_name, d.$levelField as dept_level 
                     FROM departments d 
                     JOIN programs p ON d.Department_ID = p.Department_ID 
                     JOIN sections s ON p.Program_ID = s.Program_ID 
                     WHERE s.Section_ID = :section_id",
                     
                    // Mixed column names
                    "SELECT d.$nameField as dept_name, d.$levelField as dept_level 
                     FROM departments d 
                     JOIN programs p ON d.id = p.department_id 
                     JOIN sections s ON p.program_id = s.program_id 
                     WHERE s.section_id = :section_id"
                ];
                
                foreach ($possibleQueries as $query) {
                    try {
                        $stmt = $this->conn->prepare($query);
                        $stmt->execute([':section_id' => $sectionId]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result && !empty($result['dept_name'])) {
                            return [
                                'name' => $result['dept_name'],
                                'level' => $result['dept_level'] ?? 'basic'
                            ];
                        }
                    } catch (Exception $e) {
                        // Try next query
                        continue;
                    }
                }
            } catch (Exception $e) {
                // Fall back to direct program lookup
            }
        }
        
        // Fallback: try direct program → department lookup
        if (!isset($this->tableStructure['departments']) || !isset($this->tableStructure['programs'])) {
            return $defaultDept;
        }
        
        $programId = $student['program_id'] ?? $student['Program_ID'] ?? null;
        if (!$programId) {
            return $defaultDept;
        }
        
        try {
            $deptColumns = $this->tableStructure['departments'];
            
            // Find department name field
            $nameField = 'department_name';
            if (in_array('Department_Name', $deptColumns)) {
                $nameField = 'Department_Name';
            } elseif (in_array('dept_name', $deptColumns)) {
                $nameField = 'dept_name';
            } elseif (in_array('name', $deptColumns)) {
                $nameField = 'name';
            }
            
            // Find department level field
            $levelField = 'department_level';
            if (in_array('Department_Level', $deptColumns)) {
                $levelField = 'Department_Level';
            } elseif (in_array('level', $deptColumns)) {
                $levelField = 'level';
            }
            
            // Build query to get department through program
            $sql = "SELECT d.$nameField as dept_name, d.$levelField as dept_level 
                    FROM departments d 
                    JOIN programs p ON d.department_id = p.department_id 
                    WHERE p.program_id = :program_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':program_id' => $programId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'name' => $result['dept_name'],
                    'level' => $result['dept_level'] ?? 'basic'
                ];
            }
            
            return $defaultDept;
            
        } catch (Exception $e) {
            return $defaultDept;
        }
    }
    
    /**
     * Get health record for student
     */
    public function getHealthRecord($studentId) {
        try {
            $sql = "SELECT * FROM Health_Questionnaires WHERE student_id = :student_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':student_id' => $studentId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting health record: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save health questionnaire data
     */
    public function saveHealthQuestionnaire($studentId, $data) {
        try {
            $this->conn->beginTransaction();
            
            // Check if record exists
            $existingRecord = $this->getHealthRecord($studentId);
            
            if ($existingRecord) {
                // Update existing record
                $result = $this->updateHealthRecord($studentId, $data);
            } else {
                // Create new record
                $result = $this->createHealthRecord($studentId, $data);
            }
            
            if ($result) {
                $this->conn->commit();
                return [
                    'success' => true,
                    'message' => 'Health questionnaire saved successfully',
                    'student_id' => $studentId
                ];
            } else {
                $this->conn->rollback();
                return [
                    'success' => false,
                    'message' => 'Failed to save health questionnaire'
                ];
            }
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error saving health questionnaire: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred'
            ];
        }
    }
    
    /**
     * Create new health record
     */
    private function createHealthRecord($studentId, $data) {
        try {
            // Get student info to auto-populate fields
            $studentInfo = $this->getStudentInfo($studentId);
            
            // Auto-determine education level based on department and program
            $educationLevel = 'college'; // Default to college since most programs are college level
            if ($studentInfo) {
                $deptLevel = isset($studentInfo['department_level']) ? strtolower(trim($studentInfo['department_level'])) : '';
                $programName = isset($studentInfo['program_name']) ? strtolower(trim($studentInfo['program_name'])) : '';

                // Check if it's explicitly a basic education level
                $basicEducationIndicators = ['grade', 'elementary', 'primary', 'kindergarten'];
                $isBasicEducation = false;
                
                // Check department level for basic education
                foreach ($basicEducationIndicators as $indicator) {
                    if (strpos($deptLevel, $indicator) !== false) {
                        $isBasicEducation = true;
                        break;
                    }
                }
                
                // Check program name for basic education
                if (!$isBasicEducation) {
                    foreach ($basicEducationIndicators as $indicator) {
                        if (strpos($programName, $indicator) !== false) {
                            $isBasicEducation = true;
                            break;
                        }
                    }
                }

                // If it's basic education, set to basic, otherwise keep as college
                if ($isBasicEducation) {
                    $educationLevel = 'basic';
                }
                
                // Force college for common college indicators
                $collegeProgramIndicators = ['bs', 'ba', 'bse', 'bachelor', 'associate', 'diploma', 'certificate', 'college'];
                foreach ($collegeProgramIndicators as $indicator) {
                    if (strpos($programName, $indicator) !== false || strpos($deptLevel, $indicator) !== false) {
                        $educationLevel = 'college';
                        break;
                    }
                }
            }

            $sql = "INSERT INTO Health_Questionnaires (
                student_id, education_level, student_sex, student_birthday, student_age,
                home_address, height, weight, blood_pressure, heart_rate, respiratory_rate,
                temperature, submitted_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare insert statement for student $studentId");
                return false;
            }

            $params = [
                $studentId,
                $data['education_level'] ?? $educationLevel,
                $data['student_sex'] ?? $data['sex'] ?? null,
                $data['student_birthday'] ?? $data['birth_date'] ?? null,
                $data['student_age'] ?? $data['age'] ?? null,
                $data['home_address'] ?? null,
                $data['height'] ?? null,
                $data['weight'] ?? null,
                $data['blood_pressure'] ?? null,
                $data['heart_rate'] ?? null,
                $data['respiratory_rate'] ?? null,
                $data['temperature'] ?? null
            ];

            $result = $stmt->execute($params);
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Insert failed for student $studentId: " . implode(", ", $errorInfo));
            }

            return $result;
            
        } catch (Exception $e) {
            error_log("Error creating health record for student $studentId: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing health record
     */
    private function updateHealthRecord($studentId, $data) {
        try {
            $updateFields = [];
            $params = [];

            // Build dynamic update query based on provided data
            $fieldMappings = [
                'education_level' => 'education_level',
                'student_sex' => 'student_sex',
                'sex' => 'student_sex',
                'student_birthday' => 'student_birthday',
                'birth_date' => 'student_birthday',
                'student_age' => 'student_age',
                'age' => 'student_age',
                'home_address' => 'home_address',
                'height' => 'height',
                'weight' => 'weight',
                'blood_pressure' => 'blood_pressure',
                'heart_rate' => 'heart_rate',
                'respiratory_rate' => 'respiratory_rate',
                'temperature' => 'temperature'
            ];

            foreach ($fieldMappings as $dataKey => $dbField) {
                if (isset($data[$dataKey])) {
                    $updateFields[] = "$dbField = ?";
                    $params[] = $data[$dataKey];
                }
            }

            if (empty($updateFields)) {
                error_log("No valid fields to update for student $studentId");
                return false;
            }

            // Add updated_at timestamp
            $updateFields[] = "updated_at = NOW()";
            
            // Add student ID for WHERE clause
            $params[] = $studentId;

            $sql = "UPDATE Health_Questionnaires SET " . implode(', ', $updateFields) . " WHERE student_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare update statement for student $studentId");
                return false;
            }

            $result = $stmt->execute($params);
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Update failed for student $studentId: " . implode(", ", $errorInfo));
            }

            return $result;
            
        } catch (Exception $e) {
            error_log("Error updating health record for student $studentId: " . $e->getMessage());
            return false;
        }
    }
}
?>
