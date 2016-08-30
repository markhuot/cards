/** closest **/
function closest (el, className) {
  while (el && (!el.classList || !el.classList.contains(className))) {
    el = el.parentNode;
  }

  return el;
}

/** indexOfNode **/
function indexOfNode (el) {
  var index = 0;
  el = el.previousSibling;

  while (el.previousSibling) {
    if (el.nodeType != Node.TEXT_NODE) {
      index++;
    }
    el = el.previousSibling;
  }

  return index;
}

/** serialize **/
function serialize (obj, prefix) {
  var str = [];
  for (var p in obj) {
    if (obj.hasOwnProperty(p)) {
      var k = prefix ? prefix + '[' + p + ']' : p, v = obj[p];
      str.push(typeof v == 'object' ?
        serialize(v, k) :
        encodeURIComponent(k) + '=' + encodeURIComponent(v));
    }
  }
  return str.join('&');
}

var csrfToken = document.getElementById('csrf-token').getAttribute('content');
var activeClicks = [];
var dragging = false;
var offsetX = 0;
var offsetY = 0;
var cards = [];

document.body.addEventListener('dragstart', function (event) {
  event.preventDefault();
});

document.body.addEventListener('mousedown', function (event) {
  var card = closest(event.target, 'card');
  if (!card) { return; }

  var el = event.target;
  offsetX = event.offsetX;
  offsetY = event.offsetY;
  while (!el.classList.contains('card')) {
    offsetX += el.offsetLeft;
    offsetY += el.offsetTop;
    el = el.parentNode;
  }

  activeClicks.push(setTimeout(function () {
    dragging = card;
    dragging.classList.add('dragging');
    dragging.style.width = card.offsetWidth;

    cards = document.querySelectorAll('.card');
  }, 500));
});

function dragEnd (event) {
  for (var i = 0, len = activeClicks.length; i < len; i++) {
    clearTimeout(activeClicks[i]);
  }

  if (dragging === false) { return; }
  event.preventDefault();

  var data = {'card': {}, 'stack':[]};

  dragging.parentNode.parentNode.removeChild(dragging.parentNode);

  var placeholder = document.getElementById('drag-placeholder');
  var listItem = document.createElement('li');
  listItem.appendChild(dragging);
  placeholder.parentNode.insertBefore(listItem, placeholder);
  placeholder.parentNode.removeChild(placeholder);

  var stack = closest(dragging, 'stack');
  data.card.stack_id = stack.dataset.stackId;
  var stackCards = stack.querySelectorAll('.card');
  for (var i = 0, len = stackCards.length; i < len; i++) {
    var stackCard = stackCards[i];
    var index = indexOfNode(stackCard.parentNode);
    console.debug(stackCard, index);
    data.stack.push({
      'card_id': stackCard.dataset.cardId,
      'order': index
    });
  }
  var uri = '/cards/' + dragging.dataset.cardId + '/move';
  var r = new XMLHttpRequest();
  r.open('POST', uri, true);
  r.setRequestHeader('X-CSRF-TOKEN', csrfToken);
  r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
  r.onreadystatechange = function () {
    if (r.readyState != 4 || r.status != 200) return;
  };
  r.send(serialize(data));

  dragging.classList.remove('dragging');
  dragging.style.position = 'relative';
  dragging.style.top = 0;
  dragging.style.left = 0;
  dragging.style.width = 'auto';
  dragging = false;
}

document.body.addEventListener('mouseup', dragEnd);
document.body.addEventListener('click', dragEnd);

document.body.addEventListener('mousemove', function (event) {
  if (dragging) {
    dragging.style.position = 'absolute';
    dragging.style.left = event.clientX - offsetX;
    dragging.style.top = event.clientY - offsetY;

    for (var i = 0, len = cards.length; i < len; i++) {
      var card = cards[i];

      if (card.classList.contains('dragging')) {
        continue;
      }

      if (event.clientX > card.offsetLeft && event.clientX < card.offsetLeft + card.offsetWidth &&
          event.clientY > card.offsetTop && event.clientY < card.offsetTop + card.offsetHeight) {
        var before = event.clientY < card.offsetTop + (card.offsetHeight / 2);
        insertPlaceholder(card, before);
      }
    }
  }
});

function insertPlaceholder (card, before) {
  var placeholder = document.getElementById('drag-placeholder');
  var li = card.parentNode;

  if (!placeholder) {
    placeholder = document.createElement('li');
    placeholder.id = 'drag-placeholder';
  }

  if (before) {
    li.parentNode.insertBefore(placeholder, li);
  }
  else if (!before && li.nextSibling) {
    li.parentNode.insertBefore(placeholder, li.nextSibling);
  }
  else {
    li.parentNode.appendChild(placeholder);
  }
}

document.body.addEventListener('click', function (event) {
  if (event.target.getAttribute('name') != 'line') { return; }

  var lineNo = event.target.getAttribute('value');
  var card = closest(event.target, 'card-detail__description');
  var comment = closest(event.target, 'comment-detail');
  var uri = '';

  if (card) {
    uri = '/cards/' + card.dataset.cardId + '/check'
  }
  else if (comment) {
    uri = '/comments/' + comment.dataset.commentId + '/check'
  }
  else {
    throw "Could not find the source object of this task list.";
  }

  var r = new XMLHttpRequest();
  r.open('POST', uri, true);
  r.setRequestHeader('X-CSRF-TOKEN', csrfToken);
  r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
  r.onreadystatechange = function () {
    if (r.readyState != 4 || r.status != 200) return;
  };
  r.send(serialize({
    'line': lineNo,
    'value': event.target.checked ? 1 : 0
  }));
});
