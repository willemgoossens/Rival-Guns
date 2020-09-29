import counter from '../helpers/counter.js';
import progressBar from '../helpers/progressBar.js';

$(document).ready(function()
{
    let counterElem = $("#counter");
    counter(counterElem);

    let progressBarElem = $('#progressBar');
    progressBar(progressBarElem);
});