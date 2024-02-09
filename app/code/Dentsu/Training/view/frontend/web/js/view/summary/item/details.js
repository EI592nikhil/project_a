define(
 
    [
 
        'uiComponent'
 
    ],
 
    function (Component) {
 
        "use strict";
 
        var quoteItemData = window.checkoutConfig.quoteItemData;
 
        return Component.extend({
 
            defaults: {
 
                template: 'Dentsu_Training/summary/item/details'
 
            },
 
            quoteItemData: quoteItemData,
 
            getValue: function(quoteItem) {
 
                return quoteItem.name;
 
            },
 
            getCustomValue: function(quoteItem) {
 
                var item = this.getItem(quoteItem.item_id);
 
                if(item.type_of_course_attribute){
 
                    return item.type_of_course_attribute;
 
                }else{
 
                    return '';
 
                }
 
            },
 
            getItem: function(item_id) {
 
                var itemElement = null;
 
                _.each(this.quoteItemData, function(element, index) {
 
                    if (element.item_id == item_id) {
 
                        itemElement = element;
 
                    }
 
                });
 
                return itemElement;
 
            }
 
        });
 
    }
 
);