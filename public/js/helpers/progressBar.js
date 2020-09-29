function progressBar( progressBar )
{
    let valueMin = progressBar.data("valuemin");
    let valueMax = progressBar.data("valuemax");
    let valueNow = progressBar.data("valuenow");

    let width = (valueNow / valueMax) * 100;
    progressBar.width(width + "%");

    let interval = setInterval(() => {
        let width = (valueNow / valueMax) * 100;
        console.log(width);
        progressBar.animate({"width": width + "%"});

        valueNow--;

        if(valueNow < valueMin)
        {
            clearInterval(interval);
        }
    }, 1000);
}

export default progressBar;