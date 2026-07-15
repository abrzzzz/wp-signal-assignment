<?php

namespace App\Infra\WP;

use App\Domain\StateMachine\SignalState;
use InvalidArgumentException;

class PostMetaManager
{
    public const META_BASE_QUOTE = 'base_quote';

    public const META_ENTRY_PRICE = 'entry_price';

    public const META_STOP_LOSS = 'stop_loss';

    public const META_TAKE_PROFIT = 'take_profit';

    public const META_EXPIRATION = 'expiration';

    public const META_STATUS = 'signal_status';

    public function register()
    {

        add_action('add_meta_boxes', [$this, 'addMetaBox']);

        add_action('save_post_signal', [$this, 'saveMetaBox']);

    }

    public function addMetaBox()
    {
        add_meta_box(
            'signal_details',
            'Signal Details',
            [$this, 'renderMetaBox'],
            'signal',
            'normal',
            'high',
        );
    }

    public function renderMetaBox()
    {
        global $post;
        wp_nonce_field('signal_detail_save_meta', 'signal_meta_nonce');
        $base_quote = get_post_meta($post->ID, self::META_BASE_QUOTE, true);
        $entry_price = get_post_meta($post->ID, self::META_ENTRY_PRICE, true);
        $stop_loss = get_post_meta($post->ID, self::META_STOP_LOSS, true);
        $take_profit = get_post_meta($post->ID, self::META_TAKE_PROFIT, true);
        $expiration = get_post_meta($post->ID, self::META_EXPIRATION, true);
        $status = get_post_meta($post->ID, self::META_STATUS, true);
        ?>
        <style>
            .signal-meta-table { width: 100%; }
            .signal-meta-table td { padding: 8px 0; vertical-align: top; }
            .signal-meta-table label { font-weight: 600; display: block; margin-bottom: 4px; }
            .signal-meta-table input { width: 100%; max-width: 300px; }
        </style>
        
        <table class="signal-meta-table">
            <tr>
                <td>
                    <label for="<?php echo self::META_BASE_QUOTE; ?>">Base Quote </label>
                    <input type="text" id="<?php echo self::META_BASE_QUOTE; ?>" name="<?php echo self::META_BASE_QUOTE; ?>" value="<?php echo esc_attr($base_quote); ?>" placeholder="BTC" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo self::META_ENTRY_PRICE; ?>">Entry Price</label>
                    <input type="number" step="any" id="<?php echo self::META_ENTRY_PRICE; ?>" name="<?php echo self::META_ENTRY_PRICE; ?>" value="<?php echo esc_attr($entry_price); ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo self::META_STOP_LOSS; ?>">Stop Loss</label>
                    <input type="number" step="any" id="<?php echo self::META_STOP_LOSS; ?>" name="<?php echo self::META_STOP_LOSS; ?>" value="<?php echo esc_attr($stop_loss); ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo self::META_TAKE_PROFIT; ?>">Take Profit</label>
                    <input type="number" step="any" id="<?php echo self::META_TAKE_PROFIT; ?>" name="<?php echo self::META_TAKE_PROFIT; ?>" value="<?php echo esc_attr($take_profit); ?>" />
                </td>
            </tr>
           <tr>
                <td>
                    <label for="<?php echo self::META_EXPIRATION; ?>">Expiration</label>
                    <input type="date" step="any" id="<?php echo self::META_EXPIRATION; ?>" name="<?php echo self::META_EXPIRATION; ?>" value="<?php echo esc_attr($expiration); ?>" />
                </td>
            </tr>
<tr>
    <td>
        <label for="<?php echo self::META_STATUS; ?>">Status</label>

        <select
            id="<?php echo self::META_STATUS; ?>"
            name="<?php echo self::META_STATUS; ?>"
        >
            <?php foreach (SignalState::cases() as $state) { ?>
                <option
                    value="<?php echo esc_attr($state->name); ?>"
                    <?php selected($status, $state->name); ?>
                >
                    <?php echo esc_html(
                        ucwords(str_replace('_', ' ', strtolower($state->name))),
                    ); ?>
                </option>
            <?php } ?>
        </select>
    </td>
</tr>

        </table>
        <?php
    }

    public function saveMetaBox($post_id)
    {
        if (! isset($_POST['signal_meta_nonce']) || ! wp_verify_nonce($_POST['signal_meta_nonce'], 'signal_detail_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = [
            self::META_BASE_QUOTE => 'sanitize_text_field',
            self::META_ENTRY_PRICE => 'floatval',
            self::META_STOP_LOSS => 'floatval',
            self::META_TAKE_PROFIT => 'floatval',
            self::META_EXPIRATION => 'sanitize_text_field',
        ];

        foreach ($fields as $field => $clean_func) {
            if (! isset($field)) {
                throw new InvalidArgumentException('All Signal detail  fields are required');
            }
        }

        if (! ($_POST[self::META_STOP_LOSS] < $_POST[self::META_ENTRY_PRICE])
         && ! ($_POST[self::META_ENTRY_PRICE] < $_POST[self::META_TAKE_PROFIT])) {
            throw new InvalidArgumentException('entry price must be bigger that stop loss and less than take profit');
        }
        $status = get_post_meta($post_id, self::META_STATUS, true);
        if (! $status) {
            update_post_meta($post_id, self::META_STATUS, SignalState::ACTIVE->name);
        }
        foreach ($fields as $field => $clean_func) {
            $sanitized = call_user_func($clean_func, $_POST[$field]);
            update_post_meta($post_id, $field, $sanitized);
        }
    }
}
