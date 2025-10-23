// Form functions for student dashboard

// Function to request medical certificate
function requestMedicalCertificate() {
    Swal.fire({
        title: 'Request Medical Certificate',
        html: `
            <form id="medCertForm" style="text-align: left;">
                <div style="margin-bottom: 15px;">
                    <label for="certPurpose" style="display: block; margin-bottom: 5px; font-weight: 600;">Purpose:</label>
                    <select id="certPurpose" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                        <option value="">Select Purpose</option>
                        <option value="school_requirements">School Requirements</option>
                        <option value="work_clearance">Work Clearance</option>
                        <option value="sports_participation">Sports Participation</option>
                        <option value="travel">Travel</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="certNotes" style="display: block; margin-bottom: 5px; font-weight: 600;">Additional Notes:</label>
                    <textarea id="certNotes" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;" placeholder="Any specific requirements or notes..."></textarea>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="contactNumber" style="display: block; margin-bottom: 5px; font-weight: 600;">Contact Number:</label>
                    <input type="tel" id="contactNumber" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Your contact number" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Request',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const purpose = document.getElementById('certPurpose').value;
            const notes = document.getElementById('certNotes').value;
            const contact = document.getElementById('contactNumber').value;

            if (!purpose || !contact) {
                Swal.showValidationMessage('Please fill in all required fields');
                return false;
            }

            return { purpose, notes, contact };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Submitting...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepare data for submission
            const submitData = {
                purpose: result.value.purpose,
                notes: result.value.notes,
                contact: result.value.contact,
                student_id: window.studentData ? window.studentData.student_id : null
            };

            // Send data to API
            fetch('medical-certificate-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(submitData)
            })
            .then(response => {
                console.log('API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('API response data:', data);

                if (data.success) {
                    // Success
                    Swal.fire({
                        title: 'Request Submitted!',
                        html: `
                            <div style="text-align: center;">
                                <p>Your medical certificate request has been submitted successfully.</p>
                                <p><strong>Reference Number:</strong> ${data.reference_number}</p>
                                <p style="color: #666; font-size: 14px;">You will be notified when your certificate is ready for pickup.</p>
                                <p style="color: #666; font-size: 14px;">Processing time: 1-2 business days</p>
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    // Error
                    console.error('Submission failed:', data.message);
                    Swal.fire({
                        title: 'Submission Failed',
                        text: data.message || 'There was an error submitting your request. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Try Again'
                    });
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                Swal.fire({
                    title: 'Connection Error',
                    text: 'Unable to connect to the server. Please check your internet connection and try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Try Again'
                });
            });
        }
    });
}

function showAppointmentForm() {
    Swal.fire({
        title: 'Request Appointment',
        html: `
            <form id="appointmentForm" style="text-align: left;">
                <div style="margin-bottom: 15px;">
                    <label for="appointmentDate" style="display: block; margin-bottom: 5px; font-weight: 600;">Preferred Date:</label>
                    <input type="date" id="appointmentDate" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="appointmentTime" style="display: block; margin-bottom: 5px; font-weight: 600;">Preferred Time:</label>
                    <select id="appointmentTime" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" required>
                        <option value="">Select Time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="appointmentReason" style="display: block; margin-bottom: 5px; font-weight: 600;">Reason for Visit:</label>
                    <textarea id="appointmentReason" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;" placeholder="Please describe your concern..." required></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Request',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745',
        preConfirm: () => {
            const date = document.getElementById('appointmentDate').value;
            const time = document.getElementById('appointmentTime').value;
            const reason = document.getElementById('appointmentReason').value;

            if (!date || !time || !reason) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }

            return { date, time, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Request Submitted!',
                text: 'Your appointment request has been submitted. You will be notified once it is confirmed.',
                icon: 'success',
                confirmButtonColor: '#28a745'
            });
        }
    });
}
