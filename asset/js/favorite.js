(function() {
    $(document).ready(function() {
        var hasOmeka = typeof Omeka !== 'undefined';
        var saveSearchButton = $('#favorite_add');
        var deleteSearchButton = $('#favorite_del');

        // Save search request.
        saveSearchButton.on('click', function(e){
            console.log($(this).attr('href'));
            console.log($(this).attr('data-id'));
            
            e.preventDefault();
            e.stopPropagation();

            var messageHtml;
            
                $.ajax({
                    url: $(this).attr('href'),
                    data: {
                        resource_id:$(this).attr('data-id'),
                        
                    },
                })
                .done(function(data) {
                    console.log(data);
                    if (data.status === 'success') {
                        $(saveSearchButton).addClass('hidden');
                        $(deleteSearchButton).closest('.delete-favorite-button').removeClass('hidden');
                        $(deleteSearchButton).attr('href', data.data.url_delete);
                    }
                });
                messageHtml = hasOmeka
                    ? Omeka.jsTranslate('Your search is saved.') + '<br/>' + Omeka.jsTranslate('You can find it in your account.')
                    : 'Your search is saved.' + '<br/>' + 'You can find it in your account.';
                $('.popup-message', popup).html(messageHtml);
            

            var popup = $(e.target).closest('.popup');
            popup.addClass('with-message');
            
        });
        
         // Delete search request.
        deleteSearchButton.on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            console.log('del');
            $.ajax(deleteSearchButton.attr('href'))
            .done(function(data) {
                console.log(data);
                if (data.status === 'success') {
                    $(deleteSearchButton).closest('.delete-favorite-button').addClass('hidden');
                    $(saveSearchButton).removeClass('hidden');
                }
            });
        });
        
    });
})();
