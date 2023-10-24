function add_customer_get(){
	var xmlhttp = new XMLHttpRequest();
	//get input value
	var name = encodeURI(document.getElementById('name').value);
	var address = encodeURI(document.getElementById('address').value);
	var city = encodeURI(document.getElementById('city').value);
	//validate
	if (name != "" && address != "" && city != ""){
		//set url and inner
		var url = "add_customer_get.php?name=" + name + "&address=" + address + "&city=" + city;
		alert(url);
		var inner = "add_response";
		//open request
		xmlhttp.open('GET', url, true);
		xmlhttp.onreadystatechange = function() {
			document.getElementById(inner).innerHTML = '<img src="images/ajax_loader.png"/>';
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
				 document.getElementById(inner).innerHTML = xmlhttp.responseText;
			}
			return false;
		}
		xmlhttp.send(null);
	}else{
		alert("Please fill all the fields");
	}
}

function add_customer_post(){
	var xmlhttp = new XMLHttpRequest();
	//get input value
	var name = encodeURI(document.getElementById('name').value);
	var address = encodeURI(document.getElementById('address').value);
	var city = encodeURI(document.getElementById('city').value);
	if (name != "" && address != "" && city != ""){
		//set url and inner
		var url = "add_customer_post.php"; alert(url);
		var inner = "add_response";
		//set parameter and open request
		var params = "name=" + name + "&address=" + address + "&city=" + city;
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.onreadystatechange = function() {
			document.getElementById(inner).innerHTML = '<img src="images/ajax_loader.png"/>';
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)){
				 document.getElementById(inner).innerHTML = xmlhttp.responseText;
			}
			return false;
		}
		xmlhttp.send(params);
	}else{
		alert("Please fill all the fields");
	}
}