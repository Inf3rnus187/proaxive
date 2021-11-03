/**
 * Aside / Sidebar
 */
/** SideBar Navigation **/
var button = document.querySelector('#sidebar-navigation');
var activatedClass = 'deploy';

button.addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('pe-sidebar').classList.toggle(activatedClass);
});

// Toogle Side Bar
var buttonDeploy = document.querySelector('#sidebar-toggle');
buttonDeploy.addEventListener('click', function(e){
    e.preventDefault();
    document.querySelector('body').classList.toggle('sidebar-toggled');
});

/** */
(function() {
    var dropBtns = document.querySelectorAll('.dropdown-menu');

    function closeOpenItems() {
        openMenus = document.querySelectorAll('.submenu');
        openMenus.forEach(function(menus) {
            menus.classList.remove('active');
        });
    }

    dropBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            var
                dropContent = btn.querySelector('.submenu');
            shouldOpen = !dropContent.classList.contains('active');
            //e.preventDefault();
            // First close all open items.
            closeOpenItems();
            // Check if the clicked item should be opened. It is already closed at this point so no further action is required if it should be closed.
            if (shouldOpen) {
                // Open the clicked item.
                dropContent.classList.add('active');
            }
            e.stopPropagation();
        });
    });

})();