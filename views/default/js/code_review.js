elgg.provide('elgg.code_review');

/**
 * Register the autocomplete input.
 */
elgg.code_review.init = function() {
	$('#code-review-fix-problems-selector').change(elgg.code_review.updateFixProblemsWarning);
	elgg.code_review.updateFixProblemsWarning();

	$('.elgg-form-code-review-select').submit(function(){
		var requiresConfirmation = $('#code-review-fix-problems-selector').val() === "1";
		if (requiresConfirmation) {
			if (confirm(elgg.echo('code_review:js:confirm_changes'))) {
				elgg.code_review.submitForm.apply(this);
			}
		} else {
			elgg.code_review.submitForm.apply(this);
		}

		return false;
	});
};

/**
 * Show or hide warning box, depending on dropdown selection.
 */
elgg.code_review.updateFixProblemsWarning = function() {
	var $item = $('#code-review-fix-problems-module');
	if ($('#code-review-fix-problems-selector').val() === "1") {
		$item.removeClass('hidden');
	} else {
		$item.addClass('hidden');
	}
};

/**
 * Performs form action and handles AJAX result
 */
elgg.code_review.submitForm = function () {
	$('#code-review-loader').removeClass('hidden');
	$('#code-review-result').html('');
	elgg.get('ajax/view/code_review/analysis', {
		data: $(this).serialize(),
		success: function(data){
			$('#code-review-result').html(data);
		},
		error: function(jqXHR, textStatus, errorThrown){
			elgg.register_error(elgg.echo('code_review:error:request'));
		},
		complete: function(){
			$('#code-review-loader').addClass('hidden');
		}
	});
};

elgg.register_hook_handler('init', 'system', elgg.code_review.init);