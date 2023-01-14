import Sortable from 'sortablejs';
import '../../styles/components/dragndrop.scss';

const getRandomNumber = (maxNum) => {
  return Math.floor(Math.random() * maxNum);
};

const getRandomColor = () => {
  const h = getRandomNumber(360);
  const s = 20+getRandomNumber(60);
  const l = 50+ getRandomNumber(30);

  // return `hsl(${h}deg,70%,70%,0.8)`;
  return `hsl(${h}deg, ${s}%, ${l}%)`;
};

const setBackgroundColor = (itemElement) => {
  const randomColor = getRandomColor();
  itemElement.css("backgroundColor", randomColor);
};

$(function () {
  $('.sortable-item').each(function () {
    setBackgroundColor($(this));
  });

});

var sortableElement = document.getElementById('sortable');
var sortable = Sortable.create(sortableElement, {
  handle: '.sortable-item',
  animation: 450,
  onEnd: () => {
    document.getElementById(sortableElement.dataset.newOrderKey).value = sortable.toArray();
  }
});

