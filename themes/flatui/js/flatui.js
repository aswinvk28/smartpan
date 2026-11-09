/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($) {
    $(document).ready(function() {
        if($('.feature-li-content[data-feature]').length > 0) {
            var func = _.template($('#svg-checkbox').html());
            var $html = func({});
            
            $('.feature-li-content[data-feature="1"]').html($html);
        }
        
        if($('.flatui-select').length > 0) {
            var value = $('.flatui-select-inverse.invisible').width();
            $('.flatui-select').width(value);
        }
        
        $('input, textarea').placeholder();
    });
    
    if(!!window.Braintree && $('#order-transaction-form').length != 0) {
        var braintree = Braintree.create(drupalSettings.braintree.cse);
        braintree.onSubmitEncryptForm('order-transaction-form');
        $('#edit-submit').click(function(event) {
            $('#order-transaction-form').submit();
            event.preventDefault();
        })
    }
})(window.jQuery);