<?php

/**
 * Class Es_Messenger
 */
class Es_Flash_Message
{
    /**
     * Container messages key.
     *
     * @var string
     */
    protected $_key;

    /**
     * Es_Messenger constructor.
     *
     * @param $key
     *    Message container key.
     */
    public function __construct( $key ) {
        $this->_key = $key;
    }

    /**
     * @param $errors WP_Error
     */
    public function set_wp_error( $errors ) {
        foreach ( $errors->get_error_messages() as $error ) {
            $this->set_message( $error, 'error' );
        }
    }

    /**
     * Set new message
     *
     * @param $message
     * @param $type
     */
    public function set_message( $message, $type = 'success' ) {
        $messages = $this->get_messages();
        $messages[ $type ][] = $message;
        update_option( 'es_flash_' . $this->_key, $messages );
    }

    /**
     * Render all messages and clear container.
     *
     * @return void
     */
    public function render_messages() {
        if ( $messages_list = $this->get_messages() ) {
            echo "<ul class='es-notify-list'>";
            foreach ( $messages_list as $type => $messages ) {
                if ( ! empty( $messages ) ) {
	                $icon = $type == 'success' ? 'check-mark' : $type;
	                $icon = $type == 'warning' ? 'info' : $icon;
                    foreach ( $messages as $message ) {
                        echo "<li class='es-notify es-notify--{$type}'><span class='es-icon es-icon_{$icon}'></span><p>{$message}</p></li>";
                    }
                }
            }
            echo "</ul>";

            $this->clean_container();
        }
    }

    /**
     * Return message container.
     *
     * @return null|array
     */
    public function get_messages() {
        return get_option( 'es_flash_' . $this->_key, array() );
    }

    /**
     * Clean message container.
     *
     * @return void.
     */
    public function clean_container() {
        delete_option( 'es_flash_' . $this->_key );
    }
}
