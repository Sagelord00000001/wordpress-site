<?php

class Es_Elementor_Select2_Control extends Elementor\Control_Select2 {

	/**
	 * @return string
	 */
	public function get_type() {
		return 'select2';
	}

	/**
	 * Render select2 control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() { ?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
			<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php $this->print_control_uid(); ?>" data-value="{{data.controlValue}}" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
					var value = data.controlValue;
					if ( typeof value == 'string' ) {
					var selected = ( option_value === value ) ? 'selected' : '';
					} else if ( null !== value ) {
					var value = _.values( value );
					var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
					}
					#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}