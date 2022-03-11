function setCurrentDate() {

    let currentDate = new Date();

    let day = currentDate.getDate();
    if (day < 10) day = `0${day}`;

    let month = currentDate.getMonth() + 1;
    if (month < 10) month = `0${month}`;

    let today = currentDate.getFullYear() + `-${month}-${day}`;

    document.querySelector('input[type="date"]').value = today;
}