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