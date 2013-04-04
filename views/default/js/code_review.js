elgg.provide('elgg.code_review');

/**
 * Register the autocomplete input.
 */
elgg.code_review.init = function() {
	$('.elgg-form-code-review-select').submit(function(){
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
		
		return false;
	});
};

elgg.register_hook_handler('init', 'system', elgg.code_review.init);