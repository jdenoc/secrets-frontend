/**
 * Created by denis on 5/25/14.
 */

var entryCount, current, isLoading=false, activeSecret, revealPassword;

var loading = {
    start: function(){
        if(!isLoading){
            isLoading = true;
            // add the overlay with loading image to the page
            var over = '<div id="overlay"><img id="loading" src="imgs/loader.gif" alt="loading"/></div>';
            $(over).appendTo('body');

            // click on the overlay to remove it
            $('#overlay').click(function() {
                $(this).remove();
                isLoading = false;
            });

            // hit escape to close the overlay
            $(document).keyup(function(e) {
                if (e.which === 27) {
                    $('#overlay').remove();
                    isLoading = false;
                }
            });
        }
    },
    end: function(){
        if(isLoading){
            isLoading = false;
            $('#overlay').delay(250).queue(function(){
                $(this).remove();
                $(this).dequeue();
            });
        }
    }
};

var paging = {
    next: function(){
        current++;
        $('#prev').show().val(current-1);
        $('#next').val( current+1 );
        if((current+1)*paging.limit() >= entryCount){
            $("#next").hide();
        }
        refreshTable(false);
    },
    prev: function(){
        current--;
        $('#prev').val( current-1 );
        $('#next').show().val( current );
        if(current <= 0){
            $("#prev").hide();
            current = 0;
        }
        refreshTable(false);
    },
    reset: function(){
        if(entryCount == -1){
            setTimeout(paging.reset, 500);
        } else {
            current=0;
            $('#prev').val( 0).hide();
            $('#next').val( current+1);
            if((current+1)*paging.limit() >= entryCount){
                $("#next").hide();
            } else {
                $("#next").show();
            }
        }
    },
    limit: function(){
        return 50;
    }
};

function displayAllSecrets(){
    entryCount = -1;
    $.ajax({
        type: 'POST',
        url: '../includes/get_secret_count.php?x='+nocache(),
        data: {
            user: user
        },
        beforeSend:function(){},
        success: function(data){
            entryCount = parseInt(data);
        },
        error:function(){}
    });

    $.ajax({
        type: 'POST',
        url: '../includes/get_secret_names.php?x='+nocache(),
        data: {
            limit : current,
            user: user
        },
        beforeSend:function(){},
        success:function(data){
            $('table').append(data);
            loading.end();
        },
        error:function(){}
    });
}

function displaySecret(secret_id){
    $.ajax({
        type: 'POST',
        url: '../includes/display_secret.php?x='+nocache(),
        data: {
            user: user,
            secret_id: secret_id
        },
        beforeSend:function(){
            if($(".display_secret") != []){
                $('.display_secret').parents('tr').remove();
            }
        },
        success:function(data){
            // successful request
            if(activeSecret != secret_id){
                activeSecret = secret_id;
                $('#secret_'+secret_id).after(data);
                loading.end();
            } else {
                activeSecret = null;
            }
        },
        error:function(){}
    });
}

var secretField = {
    edit: function(input){
        if(parseInt(input)==1 || parseInt(input)==2 || parseInt(input)==3){
            $('.display_secret label:nth-child('+input+') input').prop('readonly', false);
        } else if(parseInt(input)==4){
            $('.display_secret textarea').prop('readonly', false);
        }
        $('.display_secret label:nth-child('+input+') button').hide();
        $('.display_secret .glyphicon-floppy-saved').show();
    },
    reveal: function(){
        // TODO - make call to decrypt, passing encrypted string and user
        // TODO - when processing reveal of password, display a loading gif for a certain length of time.
        $('.display_secret label:nth-child(2) input').attr('type', 'text');
    },
    unreveal: function(){
        $('.display_secret label:nth-child(2) input').attr('type', 'password').val('');
    }
};

function nocache(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

$(function(){
    current = 0;
    loading.start();
    displayAllSecrets();
    paging.reset();
    $('#next').val(current+1).click(paging.next);
    $('#prev').hide().click(paging.prev);
    // TODO - count the amount of secrets, then assign
});