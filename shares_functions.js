function checkAmount(){
    var amount = parseInt(document.getElementById('amount-of-shares').value);
    
    if (amount > 0)
        return true;
    else
        return false;
}