/***************************
 * Proaxive 1.6.0.1
 * Main JS file
 * Ver.0.8
 ***************************/




/******************
 * 05.Notifications
 ******************/


/*
05.Notifications
 */
setTimeout( function(){
    const oMsg = document.getElementById('toast');
    oMsg.style.display = 'none';
}, 6000);

/*
Scroll top navbar
*/
let scrollpos = window.scrollY
const header = document.querySelector(".pe-top-navbar")
const header_height = header.offsetHeight

const add_class_on_scroll = () => header.classList.add("fixed-header")
const remove_class_on_scroll = () => header.classList.remove("fixed-header")

window.addEventListener('scroll', function() {
	scrollpos = window.scrollY;

	if (scrollpos >= header_height) { add_class_on_scroll() }
	else { remove_class_on_scroll() }
})

/*
Account navbar menu
*/
var toClose = false

function toggle(e) {
  e.stopPropagation();
  var btn=this;
  var menu = btn.nextSibling;
  
  while(menu && menu.nodeType != 1) {
     menu = menu.nextSibling
  }
  if(!menu) return;
  if (menu.style.display !== 'block') {
    menu.style.display = 'block';
    if(toClose) toClose.style.display="none";
    toClose  = menu;
  }  else {
    menu.style.display = 'none';
    toClose=false;
  }

};
function closeAll() {
    toClose.style.display='none';
};

window.addEventListener("DOMContentLoaded",function(){
  document.querySelectorAll(".btn-toggle").forEach(function(btn){
     btn.addEventListener("click",toggle,true);
  });
});

window.onclick=function(event){
  if (toClose){
    closeAll.call(event.target);
  }
};


/*
Hamburger Navigation
 */
let hamburger = document.querySelector('.hamburger');
let menu = document.querySelector('.navbar');
let bod = document.querySelector('.container');

hamburger.addEventListener('click', function() {
  hamburger.classList.toggle('isactive');
  menu.classList.toggle('active');

});
/* Dropdown */
(function() {
    var dropBtns = document.querySelectorAll('.nav__item');
  
    function closeOpenItems() {
      openMenus = document.querySelectorAll('.nav__list--submenu');
      openMenus.forEach(function(menus) {
        menus.classList.remove('show');
      });
    }
  
    dropBtns.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        var
            dropContent = btn.querySelector('.nav__list--submenu');
            shouldOpen = !dropContent.classList.contains('show');
        //e.preventDefault();
        // First close all open items.
        closeOpenItems();
        // Check if the clicked item should be opened. It is already closed at this point so no further action is required if it should be closed.
        if (shouldOpen) {
         // Open the clicked item.
          dropContent.classList.add('show');
        }
        e.stopPropagation();
      });
    });
  
    // close menus when clicking outside of them
    window.addEventListener('click', function(event) {
      if (event.target != dropBtns) {
        // Moved the code here to its own function.
        closeOpenItems();
      }
    });
  })();

/*
Fullscreen
*/
function toggleFullScreen() {
    if ((document.fullScreenElement && document.fullScreenElement !== null) ||
        (!document.mozFullScreen && !document.webkitIsFullScreen)) {
        if (document.documentElement.requestFullScreen) {
            document.documentElement.requestFullScreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullScreen) {
            document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
}

