document.getElementById('table-view-btn').addEventListener('click', function() {
    document.getElementById('table-view').style.display = 'block';
    document.getElementById('picture-view').style.display = 'none';
});

document.getElementById('picture-view-btn').addEventListener('click', function() {
    document.getElementById('table-view').style.display = 'none';
    document.getElementById('picture-view').style.display = 'block';
});
