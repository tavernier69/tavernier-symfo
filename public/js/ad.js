$('#add-image').on('click', function(){
    //récupere les numéro des futurs champs à creer 
    const index = +$('#widgets-counter').val();
    console.log(index);
    
    //récupere le prototype des entrées
    const tmpl = $('#ad_images').attr('data-prototype').replace(/__name__/g, index);
    //J'injecte le code dans la div
    $('#ad_images').append(tmpl);
    $('#widgets-counter').val(index + 1);

    handleDeleteButtons();
});

function updateCounter(){
    const count = +$('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}

function handleDeleteButtons(){
    $('button[data-action="delete"]').on('click', function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}
updateCounter();
handleDeleteButtons();