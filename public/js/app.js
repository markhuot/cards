/** closest **/
function closest(el, className) {
  while(el && (!el.classList || !el.classList.contains(className))) {
    el = el.parentNode;
  }

  return el;
}

var activeClicks = [];
var dragging = false;

document.body.addEventListener('mousedown', function (event) {
  var card = closest(event.target, 'card');

  activeClicks.push(setTimeout(function () {
    dragging = card;
    dragging.classList.add('dragging');
  }, 500));
});

document.body.addEventListener('mouseup', function (event) {
  if (dragging == false) { return; }

  dragging.style.position = 'static';
  dragging = false;

  for (var i = 0, len = activeClicks.length; i < len; i++) {
    clearTimeout(activeClicks[i]);
  }
});

document.body.addEventListener('mousemove', function (event) {
  if (dragging) {
    dragging.style.position = 'absolute';
    dragging.style.left = event.clientX;
    dragging.style.top = event.clientY;
  }
});
