var UINotifications = function() {"use strict";
	//function to initiate Toastr notifications
	var initToastr = function() {
		var i = -1;
		var toastCount = 0;
		var $toastlast;

		var getMessage = function() {
			var msgs = ['My name is Inigo Montoya. You killed my father. Prepare to die!', '<div><input class="input-small" value="textbox"/>&nbsp;<a href="http://johnpapa.net" target="_blank">This is a hyperlink</a></div><div><button type="button" id="okBtn" class="btn btn-primary">Close me</button><button type="button" id="surpriseBtn" class="btn" style="margin: 0 8px 0 8px">Surprise me</button></div>', 'Are you the six fingered man?', 'Inconceivable!', 'I do not think that means what you think it means.', 'Have fun storming the castle!'];
			i++;
			if(i === msgs.length) {
				i = 0;
			}

			return msgs[i];
		};
		$('#showtoast').on("click", function() {
			var shortCutFunction = 'warning';
			var msg = $('#message').text();
			var title = $('#title').text() || '';
			var $showDuration = 1000;
			var $hideDuration = 1000;
			var $timeOut = 5000;
			var $extendedTimeOut = 1000;
			var $showEasing = 'swing';
			var $hideEasing = 'linear';
			var $showMethod = 'fadeIn';
			var $hideMethod = 'fadeOut';
			var toastIndex = toastCount++;

			toastr.options = {
				closeButton: false,
				positionClass: 'toast-bottom-right',
				onclick: null
			};

			if($('#addBehaviorOnToastClick').prop('checked')) {
				toastr.options.onclick = function() {
					alert('You can perform some custom action after a toast goes away');
				};
			}

			if(!msg) {
				msg = getMessage();
			}

			$("#toastrOptions").text("Command: toastr[" + shortCutFunction + "](\"" + msg + ( title ? "\", \"" + title : '') + "\")\n\ntoastr.options = " + JSON.stringify(toastr.options, null, 2));

			var $toast = toastr[shortCutFunction](msg, title);
			// Wire up an event handler to a button in the toast, if it exists
			$toastlast = $toast;
			if($toast.find('#okBtn').length) {
				$toast.delegate('#okBtn', 'click', function() {
					alert('you clicked me. i was toast #' + toastIndex + '. goodbye!');
					$toast.remove();
				});
			}
			if($toast.find('#surpriseBtn').length) {
				$toast.delegate('#surpriseBtn', 'click', function() {
					alert('Surprise! you clicked me. i was toast #' + toastIndex + '. You could perform an action here.');
				});
			}
		});
		function getLastToast() {
			return $toastlast;
		}


		$('#clearlasttoast').on("click", function() {
			toastr.clear(getLastToast());
		});
		$('#cleartoasts').on("click", function() {
			toastr.clear();
		});
	};

	var initSweetAlert = function() {

		$(".basic-message").on("click", function(e) {
			swal({
				title: "Here's a message!",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".message-text-under").on("click", function(e) {
			swal({
				title: "Here's a message!",
				text: "It's pretty, isn't it?",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".success-message").on("click", function(e) {
			swal({
				title: "Good job!",
				text: "You clicked the button!",
				type: "success",
				confirmButtonColor: "#007AFF"
			});
			e.preventDefault
		});

		$(".warning-message").on("click", function(e) {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this imaginary file!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#007AFF",
				confirmButtonText: "Yes, delete it!",
				closeOnConfirm: false
			}, function() {
				swal("Deleted!", "Your imaginary file has been deleted.", "success");
			});

			e.preventDefault
		});
		$(".warning-message-parameter").on("click", function(e) {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this imaginary file!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel plx!",
				closeOnConfirm: false,
				closeOnCancel: false
			}, function(isConfirm) {
				if(isConfirm) {
					swal("Deleted!", "Your imaginary file has been deleted.", "success");
				} else {
					swal("Cancelled", "Your imaginary file is safe :)", "error");
				}
			});

			e.preventDefault
		});

		$(".message-custom-icon").on("click", function(e) {
			swal({
				title: "Sweet!",
				text: "Here's a custom image.",
				confirmButtonColor: "#007AFF",
				imageUrl: "http://i.imgur.com/4NZ6uLY.jpg"
			});

			e.preventDefault
		});
	};

	return {
		init: function() {
			initToastr();
			initSweetAlert();
		}
	};
}();
