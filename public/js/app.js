/** closest **/
function closest (el, className) {
  while (el && (!el.classList || !el.classList.contains(className))) {
    el = el.parentNode;
  }

  return el;
}

/** indexOfNode **/
function indexOfNode (el, debug) {
  var index = 0;
  el = el.previousSibling;

  while (el) {
    if (el.nodeType != Node.TEXT_NODE && !el.dataset.ignoreForIndex) {
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
var proxy = false;
var cards = [];
var stacks = [];

document.body.addEventListener('dragstart', function (event) {
  event.preventDefault();
});

document.body.addEventListener('mousedown', function (event) {
  var card = closest(event.target, 'card');
  if (!card) { return; }

  activeClicks.push(setTimeout(function () {
    dragging = card;
    dragging.classList.add('dragging');

    proxy = document.createElement('div');
    proxy.setAttribute('id', 'drag-proxy');
    proxy.innerHTML = '#<strong>'+card.dataset.localId+'</strong>';
    document.body.appendChild(proxy);

    cards = document.querySelectorAll('.card');
    stacks = document.querySelectorAll('.stack');
  }, 100));
});

function dragEnd (event) {
  for (var i = 0, len = activeClicks.length; i < len; i++) {
    clearTimeout(activeClicks[i]);
  }

  if (dragging === false) { return; }
  event.preventDefault();

  var data = {'card': {}, 'stack':[]};

  var placeholder = document.getElementById('drag-placeholder');
  if (!placeholder) {
    dragging.classList.remove('dragging');
    proxy.parentNode.removeChild(proxy);

    dragging = false;
    proxy = false;
    return false;
  }

  placeholder.parentNode.insertBefore(dragging.parentNode, placeholder);

  placeholder.parentNode.removeChild(placeholder);
  proxy.parentNode.removeChild(proxy);

  var stack = closest(dragging, 'stack');
  data.card.stack_id = stack.dataset.stackId;
  var stackCards = stack.querySelectorAll('.card');
  for (var i = 0, len = stackCards.length; i < len; i++) {
    var stackCard = stackCards[i];
    var index = indexOfNode(stackCard.parentNode, true);
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

  dragging = false;
  proxy = false;
}

document.body.addEventListener('mouseup', dragEnd);
document.body.addEventListener('click', dragEnd);

document.body.addEventListener('mousemove', function (event) {
  if (dragging) {
    proxy.style.position = 'absolute';
    proxy.style.left = event.clientX;
    proxy.style.top = event.clientY;

    var foundPosition = false;

    for (var i = 0, len = cards.length; i < len; i++) {
      var card = cards[i];

      if (card.classList.contains('dragging')) {
        continue;
      }

      if (event.clientX > card.offsetLeft && event.clientX < card.offsetLeft + card.offsetWidth &&
          event.clientY > card.offsetTop && event.clientY < card.offsetTop + card.offsetHeight) {
        var stack = closest(card, 'card-stack');
        var index = indexOfNode(card.parentNode);
        if (event.clientY > card.offsetTop + (card.offsetHeight / 2)) {
          index += 1;
        }
        insertPlaceholder(card, stack, index);
        foundPosition = true;
        break;
      }
    }

    if (!foundPosition) {
      for (var i=0, len = stacks.length; i < len; i++) {
        var stack = stacks[i];
        var cardStack = stack.querySelectorAll('.card-stack')[0];
        if (event.clientX > stack.offsetLeft && event.clientX < stack.offsetLeft + stack.offsetWidth &&
            event.clientY > stack.offsetTop && event.clientY < stack.offsetTop + stack.offsetHeight &&
            event.clientY > cardStack.offsetTop + cardStack.offsetHeight) {
          insertPlaceholder(card, cardStack, cardStack.querySelectorAll('.card').length);
          foundPosition = true;
          break;
        }
      }
    }
  }
});

function insertPlaceholder (card, stack, index) {
  var placeholder = document.getElementById('drag-placeholder');
  if (!placeholder) {
    placeholder = document.createElement('li');
    placeholder.id = 'drag-placeholder';
    placeholder.dataset.ignoreForIndex = true;
  }

  var children = [];
  for (var i=0, len=stack.children.length; i<len; i++) {
    if (stack.children[i].id != 'drag-placeholder') {
      children.push(stack.children[i]);
    }
  }

  if (index == 0) {
    stack.insertBefore(placeholder, stack.firstChild);
  }
  else if (children[index]) {
    stack.insertBefore(placeholder, children[index]);
  }
  else {
    stack.appendChild(placeholder);
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
