var containerGridList = document.getElementsByClassName('toggle-grid0-list');

document.addEventListener("click", function (event) {
    if (!event.target.matches(".list")) return;

    // List view
    event.preventDefault();
    for (i = 0; i < containerGridList.length; i++) {
        containerGridList[i].classList.add('item-single--list');
        containerGridList[i].classList.remove('item-single--grid0');
    }
});

document.addEventListener("click", function (event) {
    if (!event.target.matches(".grid0")) return;

    // List view
    event.preventDefault();
    for (i = 0; i < containerGridList.length; i++) {
        containerGridList[i].classList.add('item-single--grid0');
        containerGridList[i].classList.remove('item-single--list');
    }
});