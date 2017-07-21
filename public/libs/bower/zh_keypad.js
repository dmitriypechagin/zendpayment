Array.prototype.shuffle = function() {
	return this.sort(function() {
		return 0.5 - Math.random();
	});
};

function offsetPosition(element) {//получаем текущие координаты блока
	var offsetL = offsetT = 0;

	do {
		offsetL += element.offsetLeft;
		offsetT += element.offsetTop;

	} while (element = element.offsetParent);

	return [offsetL, offsetT];
}


document.getElementById("zh_PIN").onclick = function(e)
{
	var zh_PIN = this;

	var pos = offsetPosition(zh_PIN);

	var zh_keypad = document.getElementById("zh_keypad");


	if ( zh_keypad.style.display == "block" ) return false;

		zh_keypad.style.display = "block";
		zh_keypad.style.top = (pos[1] + 43) +"px";
		zh_keypad.style.left = (pos[0]) +"px";

	var numbers = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

	numbers.shuffle();

	var div;

	for ( var i = 0; i < numbers.length; i++ )
	{
		div = document.createElement("button");
			div.style.margin = "2px";
			div.style.width = "30px";
			div.style.textAlign = "center";
			div.innerHTML = numbers[i];

			div.onclick = function()
			{
				zh_PIN.value = zh_PIN.value +""+ this.innerHTML;
			};

		zh_keypad.appendChild(div);

		if ( i == 2 ) {
			div = document.createElement("button");
				div.style.cssText = "margin:2px;background-color: #0a0;color: #fff;width: 52px;";
				div.innerHTML = 'Ok';

			div.onclick = function()
			{
				zh_keypad.innerHTML = '';
				zh_keypad.style.display = 'none';
			};

			zh_keypad.appendChild(div);
		}

		if ( i == 5 ) {
			div = document.createElement("button");
				div.style.cssText = "margin:2px;background-color: #a00;color: #fff;width: 52px;";
				div.innerHTML = 'Clear';

			div.onclick = function()
			{
				zh_PIN.value = '';
			};

			zh_keypad.appendChild(div);
		}

		if ( i == 8 ) {
			div = document.createElement("button");
				div.style.cssText = "margin:2px;background-color: #00a;color: #fff;width: 52px;";
				div.innerHTML = 'Back';

			div.onclick = function()
			{
				zh_PIN.value = zh_PIN.value.substring(0, (zh_PIN.value.length - 1));
			};

			zh_keypad.appendChild(div);
		}


	}

	return false;
};