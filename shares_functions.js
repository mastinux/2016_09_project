function checkAmount(){
    var amount = parseInt(document.getElementById('amount-of-shares').value);
    
    if (amount > 0)
        return true;
    else {
        printMessage("warning", "Order amount must be greater than 0.");
        return false;
    }
}