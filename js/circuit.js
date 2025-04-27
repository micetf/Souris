$(document).ready(function () {
    // Masquer les éléments non JavaScript
    $("#jsKO").hide();

    // Configurer l'arrière-plan du circuit et du popup avec les images du parcours
    $("#circuit").css("background", "url(images/" + parcours + ".png)");
    $("#popup").css("background", "url(images/" + parcours + ".png)");

    // Initialiser le plugin de contact (si disponible)
    if ($.fn.contact) {
        $("#contact").contact();
    }

    // Variables d'état du jeu
    var ereg = new RegExp(",");
    var start = false;
    var debut;
    var fin;
    var chrono = 0;
    var pos = [0, 0];
    var oCompteur;
    var key = "";

    // Structure pour stocker les records locaux
    const localRecords = {
        init: function () {
            if (!localStorage.getItem("sourisRecords")) {
                localStorage.setItem("sourisRecords", JSON.stringify({}));
            }
            return JSON.parse(localStorage.getItem("sourisRecords"));
        },

        saveRecord: function (parcours, chrono) {
            let records = this.init();
            if (!records[parcours] || records[parcours] > chrono) {
                records[parcours] = chrono;
                localStorage.setItem("sourisRecords", JSON.stringify(records));
                return true; // Nouveau record personnel
            }
            return false; // Pas de nouveau record
        },

        getRecord: function (parcours) {
            let records = this.init();
            return records[parcours] || null;
        },
    };

    // Afficher les records locaux au chargement
    const personalBest = localRecords.getRecord(parcours);
    if (personalBest) {
        $("#personal-record")
            .show()
            .text("Ton record personnel : " + personalBest + " s");
    } else {
        $("#personal-record").hide();
    }

    // Fonction de mise à jour du chronomètre
    function Compteur() {
        fin = new Date();
        chrono = fin.getTime() - debut.getTime();
        $("#chrono").text(Math.round(chrono / 10) / 100);
        oCompteur = setTimeout(Compteur, 10);
    }

    // Gestion du clic sur le circuit (arrêt du jeu)
    $("#circuit").on("mousedown", function (event) {
        if (start) {
            event.stopPropagation();
            clearTimeout(oCompteur);
            $("#circuit").css("cursor", "auto");
            start = false;
            $("#circuit").hide();
            $("#popup")
                .show()
                .find("h1")
                .html("Perdu !")
                .css("backgroundColor", "orange");
        }
    });

    // Gestion du clic sur le bouton du popup pour recommencer
    $("#popup input").on("click", function () {
        $("#popup").hide();
        $("#circuit").show();
    });

    // Gestion du mouvement de la souris sur le circuit
    $("#circuit").on("mousemove", function (event) {
        event.stopPropagation();

        // Calcul de la position relative au circuit
        var offset = $("#circuit").offset();
        var delta_x = Math.round(event.pageX - offset.left);
        var delta_y = event.pageY - $("#circuit").scrollTop();

        // Récupération de la couleur à cette position
        var color = 0;
        if (
            zBitmap[delta_x] !== undefined &&
            zBitmap[delta_x][delta_y] !== undefined
        ) {
            color = zBitmap[delta_x][delta_y];
        }

        // Si le jeu n'est pas démarré et qu'on est sur un point de départ (vert)
        if (!start && color == 1) {
            start = true;
            debut = new Date();
            Compteur();

            pos = [delta_x, delta_y];
            $("#circuit").css("cursor", "url(images/coccinelle.cur), pointer");
        }

        // Si le jeu est démarré, vérification des déplacements
        if (start) {
            var deplacement = Math.sqrt(
                Math.pow(Math.abs(pos[0] - delta_x), 2) +
                    Math.pow(Math.abs(pos[1] - delta_y), 2)
            );

            // Vérification que le déplacement n'est pas trop grand (anti-triche)
            if (deplacement < 400) {
                pos = [delta_x, delta_y];

                // Si on sort du chemin (blanc)
                if (color == 0) {
                    clearTimeout(oCompteur);
                    $("#circuit").css("cursor", "auto");
                    start = false;
                    $("#circuit").hide();
                    $("#popup")
                        .show()
                        .find("h1")
                        .html("Perdu !")
                        .css("backgroundColor", "orange");
                }
                // Si on arrive à la fin (rouge)
                else if (color == 3) {
                    clearTimeout(oCompteur);
                    $("#circuit").css("cursor", "auto");
                    fin = new Date();
                    chrono = fin.getTime() - debut.getTime();
                    start = false;

                    // Vérifier et sauvegarder le record local
                    const chronoSeconds = chrono / 1000;
                    const isNewPersonalRecord = localRecords.saveRecord(
                        parcours,
                        chronoSeconds
                    );

                    // Mettre à jour l'affichage du record personnel
                    if (isNewPersonalRecord) {
                        $("#personal-record")
                            .show()
                            .text(
                                "Ton record personnel : " + chronoSeconds + " s"
                            );
                    }

                    // Envoyer le résultat au serveur via AJAX
                    $.post(
                        "ajax/key.php",
                        { chrono: chrono / 10, token: sessionToken },
                        function (key) {
                            $.post(
                                "ajax/record.php",
                                {
                                    parcours: parcours,
                                    pseudo: pilote,
                                    chrono: chrono / 10,
                                    key: key,
                                    token: sessionToken,
                                },
                                function (data) {
                                    if ($.trim(data) != "") {
                                        $("#records ol")
                                            .show()
                                            .empty()
                                            .append(data);
                                        $("#records ul").hide();
                                    } else {
                                        $("#records ol").hide();
                                        $("#records ul").show();
                                    }
                                }
                            );
                        }
                    );

                    // Affichage du résultat
                    $("#chrono").text(chrono / 1000);
                    $("#circuit").hide();
                    $("#popup")
                        .show()
                        .find("h1")
                        .html(
                            "Bravo !<br/>Tu as réussi en " +
                                chrono / 1000 +
                                " s" +
                                (isNewPersonalRecord
                                    ? "<br/><span class='new-record'>Nouveau record personnel !</span>"
                                    : "")
                        )
                        .css("backgroundColor", "green");
                }
            }
            // Si le déplacement est trop grand, c'est perdu
            else {
                clearTimeout(oCompteur);
                $("#circuit").css("cursor", "auto");
                start = false;
                $("#circuit").hide();
                $("#popup")
                    .show()
                    .find("h1")
                    .html("Perdu !")
                    .css("backgroundColor", "orange");
            }
        }
    });

    // Si la souris quitte la zone de jeu
    $("#jsOK").on("mousemove", function () {
        if (start) {
            clearTimeout(oCompteur);
            $("#circuit").css("cursor", "auto");
            start = false;
            $("#circuit").hide();
            $("#popup")
                .show()
                .find("h1")
                .html("Perdu !")
                .css("backgroundColor", "orange");
        }
    });

    // Fonction pour afficher le dialogue de saisie du pseudo
    var getPseudo = function () {
        $("#jsOK").hide();
        $("#getPseudo").show();
        $("#getPseudo input[type=text]").val(pilote).focus();
    };

    // Gestion du clic sur le bouton de validation du pseudo
    $("#getPseudo input[type=button]").on("click", function () {
        pilote = $.trim($("#getPseudo input[type=text]").val());
        if (pilote != null && pilote.length >= 4 && !pilote.match(ereg)) {
            $("#pseudo").text("[" + pilote + "]");
            $("#getPseudo").hide();
            $("#jsOK").show();
        }
    });

    // Gestion des différents boutons et liens
    $("#pseudo").on("click", function () {
        getPseudo();
    });

    $("#aide").on("click", function () {
        $("#jsOK").hide();
        $("#consignes").show();
    });

    $("#retour").on("click", function () {
        $("#consignes").hide();
        $("#jsOK").show();
    });

    $(".changer").on("click", function () {
        $(this).attr("href", $(this).attr("href") + "&p=" + pilote);
    });

    // Chargement des records via AJAX
    $.post("ajax/record.php", { parcours: parcours }, function (data) {
        if ($.trim(data) != "") {
            $("#records ol").show().empty().append(data);
            $("#records ul").hide();
        } else {
            $("#records ol").hide();
            $("#records ul").show();
        }
    });

    // Vérification du pseudo au chargement
    if (pilote.length < 4 || pilote.match(ereg)) {
        pilote = "Anonyme";
        getPseudo();
    } else {
        $("#getPseudo").hide();
        $("#jsOK").show();
        $("#pseudo").text("[" + pilote + "]");
    }
});
