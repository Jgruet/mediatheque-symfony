$(document).ready(function () {
  console.log('fichier js appelé');
  $('#borrowDoc').click(function (e) {
    e.preventDefault();
    let book = $(this).attr('data-book-id');
    $.ajax({
      //L'URL de la requête
      url: '/books/' + book + '/emprunter',

      //La méthode d'envoi (type de requête)
      method: 'POST',

      //Le format de réponse attendu
      dataType: 'json',
    })
      //Ce code sera exécuté en cas de succès - La réponse du serveur est passée à done()
      .done(function (response) {
        $('#exampleModal2 .modal-body').html(response.message);
        let myModal = new bootstrap.Modal($('#exampleModal2'));
        myModal.show();
        $('#borrowDoc').attr('disabled', 'disabled');
      })

      //Ce code sera exécuté en cas d'échec - L'erreur est passée à fail()
      //On peut afficher les informations relatives à la requête et à l'erreur
      .fail(function (error) {
        let messageJSON = error.responseJSON;
        let message = messageJSON.message;

        $('#exampleModal2 .modal-body').html(message);
        let myModal = new bootstrap.Modal($('#exampleModal2'));
        myModal.show();

        $('#borrowDoc').attr('disabled', 'disabled');
      });
  });
});
