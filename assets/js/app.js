jQuery(document).ready(function ($) {
	$('.rating__like-btn, .rating__dislike-btn').on('click', function () {
		const button = $(this)
		const postId = button.closest('.article__rating').data('post-id')
		const vote = button.hasClass('rating__like-btn') ? 1 : -1

		$.post(
			likeDislikeData.ajax_url,
			{
				action: 'like_dislike',
				nonce: likeDislikeData.nonce,
				post_id: postId,
				vote: vote,
			},
			function (response) {
				try {
					if (response && response.rating !== undefined) {
						const ratingElement = button.siblings('.rating__value')
						ratingElement.text(response.rating)

						ratingElement.removeClass('rating__positive rating__negative')

						if (response.rating > 0) {
							ratingElement.addClass('rating__positive')
						} else if (response.rating < 0) {
							ratingElement.addClass('rating__negative')
						}
					} else {
						console.error('Response does not contain a rating field', response)
					}
				} catch (error) {
					console.error('Error processing AJAX response:', error)
				}
			}
		).fail(function (jqXHR, textStatus, errorThrown) {
			console.error('AJAX request failed:', textStatus, errorThrown)
		})
	})
})
