function changeBox(idBox, idContent, type) {
    if (document.getElementById(idBox).checked === true) {
        document.getElementById(idBox).checked = false;
        if (type === "IMAGE") {
            document.getElementById(idContent).style.backgroundColor = 'red';
        } else if (type === "TEXTE") {
            document.getElementById(idContent).style.backgroundColor = 'red';
        }
    } else {
        document.getElementById(idBox).checked = true;
        if (type === "IMAGE") {
            document.getElementById(idContent).style.backgroundColor = 'green';
        } else if (type === "TEXTE") {
            document.getElementById(idContent).style.backgroundColor = 'green';
        }
    }
}

var timer;
var time = 0;
var tempsMax;

function Minuteur() {
	if (tempsMax === time) {
		document.getElementsByName("subbut")[0].form.submit();
	}
}

function Chrono() {
	time += 1;
	Minuteur();
}

function DemarrerChrono() {
	timer = setInterval(Chrono, tempsMax);
}

function ArreterChrono() {
	clearInterval(timer);
}

function ChronoInInput() {
	document.getElementsByName("temps")[0].setAttribute("value", time); 
}