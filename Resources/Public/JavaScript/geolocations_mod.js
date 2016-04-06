var backgroundProcess = true;
var progressbar = null;
var processButton = null;
;
(function ($) {
	$(document).ready(function ($) {
		// Oon page load...
		$(function () {
			progressbar = $("#progressbar");
			processButton = $("#start");
			processButton.attr({disabled: true});
			getStatus(true);

		});
		function setProgress(progress) {
			var inner = $('.progress .progress-bar');
			var text = $('.progress .progress-text');
			var statusText = '';
			inner.css({width: progress + '%'});
			inner.attr('aria-valuenow', progress);
			if (progress === 100) {
				backgroundProcess = false;
				processButton.attr({disabled: false});
				statusText = tx_geolocations_strings.backgroundProcessFinished;
			} else {
				statusText = tx_geolocations_strings.backgroundProcessRunning.replace('###', progress);
			}
			text.text(statusText);
		}
		function getStatus(initialCheck) {
			$.ajax({
				url: tx_geolocations_strings.checkUrl,
				type: 'get',
				dataType: 'json',
				cache: false,
				async: true,
				success: function (data) {
					var percentage = parseInt(data);
					console.log('percentage: ' + percentage);
					if (percentage >= 0) {
						progressbar.show();
						if (percentage < 100 && backgroundProcess) {
							setProgress(percentage);
							getStatus();
						} else {
							setProgress(100);
						}
					} else {
						setProgress(100);
					}
				}
			});
		}
		$("#start").click(function (event) {
			if (!backgroundProcess) {
				top.TYPO3.Modal.confirm(tx_geolocations_strings.confirmTitle, tx_geolocations_strings.geocodeLocations).on('button.clicked', function (e) {
					if (e.target.name == 'ok') {
						processButton.attr({disabled: true});
						progressbar.show();
						$.ajax({
							url: tx_geolocations_strings.startUrl,
							type: 'get',
							dataType: 'json',
							cache: false,
							async: true,
							success: function (data) {}
						});
						backgroundProcess = true;
						getStatus();
					}
					top.TYPO3.Modal.dismiss();
				});
			}
		});
	});
})(TYPO3.jQuery || jQuery);
