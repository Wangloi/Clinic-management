document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const sortFilter = document.getElementById('sortFilter');
  const studentTableBody = document.getElementById('studentTableBody');
  const rows = Array.from(document.querySelectorAll('.student-row'));
  
  // Store original order for resetting
  const originalOrder = rows.map(row => row.innerHTML);
  
  // Search functionality
  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    filterStudents(searchTerm);
  });
  
  // Sort functionality
  sortFilter.addEventListener('change', function() {
    const sortValue = this.value;
    sortStudents(sortValue);
  });
  
  // Click on headers to sort
  document.querySelectorAll('th[data-sort]').forEach(header => {
    header.addEventListener('click', function() {
      const sortKey = this.getAttribute('data-sort');
      const currentSort = sortFilter.value;
      
      // Toggle between asc/desc if clicking same column
      if (currentSort.startsWith(sortKey)) {
        sortFilter.value = currentSort.includes('asc') ? 
          `${sortKey}-desc` : `${sortKey}-asc`;
      } else {
        sortFilter.value = `${sortKey}-asc`;
      }
      
      sortStudents(sortFilter.value);
    });
  });
  
  function filterStudents(searchTerm) {
    rows.forEach(row => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(searchTerm) ? '' : 'none';
    });
  }
  
  function sortStudents(sortValue) {
    const [key, direction] = sortValue.split('-');
    
    rows.sort((a, b) => {
      const aValue = getCellValue(a, key);
      const bValue = getCellValue(b, key);
      
      // For numeric values (ID, Year)
      if (key === 'id' || key === 'year') {
        return direction === 'asc' ? 
          parseInt(aValue) - parseInt(bValue) : 
          parseInt(bValue) - parseInt(aValue);
      }
      // For date values
      else if (key === 'date') {
        return direction === 'asc' ?
          new Date(aValue) - new Date(bValue) :
          new Date(bValue) - new Date(aValue);
      }
      // For text values
      else {
        return direction === 'asc' ?
          aValue.localeCompare(bValue) :
          bValue.localeCompare(aValue);
      }
    });
    
    // Re-append sorted rows
    rows.forEach(row => studentTableBody.appendChild(row));
  }
  
  function getCellValue(row, key) {
    const cellIndex = Array.from(row.parentElement.children[0].children)
      .findIndex(th => th.getAttribute('data-sort') === key);
    return row.children[cellIndex].textContent;
  }
});