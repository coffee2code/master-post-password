document.addEventListener( 'DOMContentLoaded', function() {
	const container = document.getElementById( 'master-post-password-field' );
	const input     = container.querySelector( 'input[type="password"]' );
	const btn       = container.querySelector( 'button.wp-hide-pw' );
	const icon      = btn.querySelector( 'span.dashicons' );
	const label     = btn.querySelector( 'span.text' );
console.log('icon', icon, 'label', label);
	if ( ! input || ! btn || ! label ) {
		return;
	}

	btn.addEventListener( 'click', function() {
		const isPwd = input.type === 'password';
		input.type = isPwd ? 'text' : 'password';

		label.textContent = isPwd
			? wpMasterPostPasswordToggle.hide
			: wpMasterPostPasswordToggle.show;

		icon?.classList.toggle( 'dashicons-visibility' );
		icon?.classList.toggle( 'dashicons-hidden' );

		this.setAttribute(
			'aria-label',
			isPwd
				? wpMasterPostPasswordToggle.hideLabel
				: wpMasterPostPasswordToggle.showLabel
		);
	});
});
