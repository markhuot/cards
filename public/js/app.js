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
    if (el.nodeType != Node.TEXT_NODE /*&& !el.dataset.ignoreForIndex*/) {
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

/** send post **/
function xhrPost(uri, data) {
  var r = new XMLHttpRequest();
  r.open('POST', uri, true);
  r.setRequestHeader('X-CSRF-TOKEN', csrfToken);
  r.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
  r.onreadystatechange = function () {
    if (r.readyState != 4 || r.status != 200) return;
  };
  r.send(serialize(data));
}

var csrfToken = document.getElementById('csrf-token').getAttribute('content');
var activeClicks = [];
var dragging = false;
var proxy = false;
var cards = [];
var stacks = [];
var targetStack = false;
var targetIndex = false;

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
  if (placeholder) {
    placeholder.parentNode.removeChild(placeholder);

    if (targetStack.children[targetIndex]) {
      targetStack.insertBefore(dragging.parentNode, targetStack.children[targetIndex]);
    }
    else if (targetIndex >= targetStack.children.length) {
      targetStack.appendChild(dragging.parentNode);
    }

    var stack = closest(targetStack, 'stack');
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

    xhrPost('/cards/' + dragging.dataset.cardId + '/move', data);
  }

  dragging.classList.remove('dragging');
  proxy.parentNode.removeChild(proxy);

  dragging = false;
  proxy = false;
}

document.body.addEventListener('mouseup', dragEnd);
document.body.addEventListener('click', dragEnd);

document.body.addEventListener('mousemove', function (event) {
  if (dragging) {
    var foundPosition = false;
    var mouseX = event.pageX;
    var mouseY = event.pageY;

    proxy.style.left = mouseX;
    proxy.style.top = mouseY;

    for (var i = 0, len = cards.length; i < len; i++) {
      var card = cards[i];
      var cardStack = closest(card, 'card-stack');

      if (mouseX > card.offsetLeft && mouseX < card.offsetLeft + card.offsetWidth &&
          mouseY > card.offsetTop - cardStack.scrollTop && mouseY < card.offsetTop - cardStack.scrollTop + card.offsetHeight) {
        var index = indexOfNode(card.parentNode);
        if (mouseY > card.offsetTop - cardStack.scrollTop + (card.offsetHeight / 2)) {
          index += 1;
        }
        console.debug(card.dataset.cardId, index);
        insertPlaceholder(cardStack, index);
        foundPosition = true;
        break;
      }
    }

    if (!foundPosition) {
      for (i = 0, len = stacks.length; i < len; i++) {
        var stack = stacks[i];
        var cardStack = stack.querySelectorAll('.card-stack')[0];
        if (mouseX > stack.offsetLeft && mouseX < stack.offsetLeft + stack.offsetWidth &&
            mouseY > stack.offsetTop && mouseY < stack.offsetTop + stack.offsetHeight &&
            mouseY > cardStack.offsetTop + cardStack.offsetHeight) {
          insertPlaceholder(cardStack, cardStack.querySelectorAll('.card').length);
          foundPosition = true;
          break;
        }
      }
    }
  }
});

function insertPlaceholder (stack, index) {
  targetStack = stack;
  targetIndex = index;

  var placeholder = document.getElementById('drag-placeholder');
  if (!placeholder) {
    placeholder = document.createElement('div');
    placeholder.id = 'drag-placeholder';
    document.body.appendChild(placeholder);
  }

  if (stack.children[index]) {
    placeholder.style.top = stack.children[index].offsetTop - stack.scrollTop - 5 /* 5px = .5 of the margin-top */;
    placeholder.style.left = stack.children[index].offsetLeft;
    placeholder.style.width = stack.children[index].offsetWidth;
  }
  else if (index > 0 && index >= stack.children.length) {
    placeholder.style.top = stack.children[stack.children.length-1].offsetTop - stack.scrollTop + stack.children[stack.children.length-1].offsetHeight + 5 /* 5px = .5 of the margin-top */;
    placeholder.style.left = stack.children[stack.children.length-1].offsetLeft;
    placeholder.style.width = stack.children[stack.children.length-1].offsetWidth;
  }
  else if (index == 0) {
    placeholder.style.top = stack.offsetTop + stack.offsetHeight;
    placeholder.style.left = stack.offsetLeft;
    placeholder.style.width = stack.offsetWidth;
  }
}

document.body.addEventListener('click', function (event) {
  if (event.target.getAttribute('name') != 'line') { return; }

  var lineNo = event.target.getAttribute('value');
  var source = closest(event.target, 'card-detail__description');
  var uri = '/cards/';

  if (!source) {
    source = closest(event.target, 'comment-detail');
    uri = '/comments/';
  }

  uri += source.dataset.id + '/check'

  xhrPost(uri, {
    'line': lineNo,
    'value': event.target.checked ? 1 : 0
  });
});
