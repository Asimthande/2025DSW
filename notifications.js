document.addEventListener('DOMContentLoaded', function () {
    const notificationsList = document.getElementById('notifications-list');
    const orderSelect = document.getElementById('order-select');
    const groupSelect = document.getElementById('group-select');
  
    let notifications = [];
    let notificationCount = 1;
  
    function generateRandomNotification() {
      const categories = ['General', 'Updates', 'Alerts'];
      const randomCategory = categories[Math.floor(Math.random() * categories.length)];
      const notification = {
        id: notificationCount++,
        name: `Notification ${notificationCount}`,
        category: randomCategory,
        timestamp: new Date().toISOString(),
      };
      notifications.push(notification);
      renderNotifications();
    }
  
    function renderNotifications() {
      notificationsList.innerHTML = '';
      let sortedNotifications = [...notifications];
  
      const order = orderSelect.value;
      if (order === 'newest') {
        sortedNotifications.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
      } else if (order === 'oldest') {
        sortedNotifications.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
      } else if (order === 'name') {
        sortedNotifications.sort((a, b) => a.name.localeCompare(b.name));
      }
  
      const groupBy = groupSelect.value;
      if (groupBy === 'category') {
        const groupedNotifications = groupNotificationsByCategory(sortedNotifications);
        groupedNotifications.forEach(group => {
          const groupTitle = document.createElement('li');
          groupTitle.classList.add('group');
          groupTitle.textContent = group.category;
          notificationsList.appendChild(groupTitle);
  
          group.notifications.forEach(notification => {
            const li = document.createElement('li');
            li.textContent = notification.name;
            notificationsList.appendChild(li);
          });
        });
      } else {
        sortedNotifications.forEach(notification => {
          const li = document.createElement('li');
          li.textContent = notification.name;
          notificationsList.appendChild(li);
        });
      }
    }
  
    function groupNotificationsByCategory(notifications) {
      const groups = {};
      notifications.forEach(notification => {
        if (!groups[notification.category]) {
          groups[notification.category] = [];
        }
        groups[notification.category].push(notification);
      });
  
      return Object.keys(groups).map(category => ({
        category,
        notifications: groups[category],
      }));
    }
  
    orderSelect.addEventListener('change', function () {
      renderNotifications();
    });
  
    groupSelect.addEventListener('change', function () {
      renderNotifications();
    });
  
    setInterval(generateRandomNotification, 3000);
  });
  