/**
 * Task
 * 1. Get input from input box
 * 2. If input value length is >= 3 then send ajax request.
 * 3. Show raw company list
 */

 document.querySelector('#myInput').addEventListener('keyup', function(event) {
    var resultDom = document.getElementById('company-list');
    resultDom.style.display = 'none';
    var value = this.value;
    if(value.length >= 3) {
        //TODO Add ajax call here
        $.ajax({
            url: "ajaxGateway.php", 
            data: {'query': value},
            type: 'get',
            success: function(result){
                var data = result['data'];
                resultDom.textContent = JSON.stringify(data);
                resultDom.style.display = 'block';
        }});
    }
 });