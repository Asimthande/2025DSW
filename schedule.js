// Sample bus schedule data - In real use case, you would fetch this from the database
const scheduleData = [
    { busId: 'B001', route: 'Route 1', departure: '08:00 AM', arrival: '10:00 AM' },
    { busId: 'B002', route: 'Route 2', departure: '09:30 AM', arrival: '11:30 AM' },
    { busId: 'B003', route: 'Route 3', departure: '11:00 AM', arrival: '01:00 PM' },
    { busId: 'B004', route: 'Route 4', departure: '01:30 PM', arrival: '03:30 PM' },
    { busId: 'B005', route: 'Route 5', departure: '03:00 PM', arrival: '05:00 PM' }
];

// Get the DOM elements
const tableViewButton = document.getElementById('table-view');
const listViewButton = document.getElementById('list-view');
const tableViewContainer = document.getElementById('table-view-container');
const listViewContainer = document.getElementById('list-view-container');
const scheduleTableBody = document.getElementById('schedule-table-body');
const scheduleList = document.getElementById('schedule-list');

// Function to render the table view
function renderTableView(data) {
    scheduleTableBody.innerHTML = ''; // Clear existing data
    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.busId}</td>
            <td>${item.route}</td>
            <td>${item.departure}</td>
            <td>${item.arrival}</td>
        `;
        scheduleTableBody.appendChild(row);
    });
}

// Function to render the list view
function renderListView(data) {
    scheduleList.innerHTML = ''; // Clear existing data
    data.forEach(item => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <span>Bus ID: ${item.busId}</span><br>
            Route: ${item.route}<br>
            Departure: ${item.departure} | Arrival: ${item.arrival}
        `;
        scheduleList.appendChild(listItem);
    });
}

// Initially render the table view
renderTableView(scheduleData);

// Toggle views between table and list
tableViewButton.addEventListener('click', () => {
    tableViewButton.classList.add('active');
    listViewButton.classList.remove('active');
    tableViewContainer.style.display = 'block';
    listViewContainer.style.display = 'none';
});

listViewButton.addEventListener('click', () => {
    listViewButton.classList.add('active');
    tableViewButton.classList.remove('active');
    tableViewContainer.style.display = 'none';
    listViewContainer.style.display = 'block';
});

// Optional: On load, you can choose a default view (Table or List)
// By default, table view is selected already
