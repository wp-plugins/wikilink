/**
 * WikiLink Script (JS CORE)
 * (c) 2009 Eduardo Daniel Sada
 * www.coders.me
**/

document.addEvent('domready', function() {
    $$('.wikispan').each(function(el) {
        el.addEvent (wiki['event'], function (event) {
            if (!this.OBJtooltip) {
                new Request({
                    url         : wiki['dir']+'wikipedia-api.php',
                    data        : 'search='+this.get('text')+'&lang='+wiki['lang'],
                    onRequest   : function () {
                        this.OBJtooltip = new ToolTip(this, '<div class="wikiloader"></div>', { mouse: 0, width: wiki['width'], style: 'wiki', sticky: 1 });
                        this.OBJtooltip.show(event);
                    }.bind(this),
                    onSuccess   : function(responseText, responseXML) {
                        responseText = '<h1>'+this.get('text')+'</h1>'+responseText;
                        this.OBJtooltip.skeleton.middle.set('html', responseText);
                        this.OBJtooltip.hide();
                        this.OBJtooltip.show(event);
                    }.bind(this),
                    onFailure   : function() {
                        this.OBJtooltip.hide();
                    }.bind(this)
                }).send();
            } else {
                this.OBJtooltip.show(event);
            }
        });
    });
});