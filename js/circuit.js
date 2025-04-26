$(document).ready(function () {
    $("#jsKO").hide();
    $("#circuit").css("background", "url(images/" + parcours + ".png)");
    $("#popup").css("background", "url(images/" + parcours + ".png)");
    $("#contact").contact();

    var ereg = new RegExp(",");
    var start = false;
    var debut;
    var fin;
    var chrono = 0;
    var pos = new Array(0, 0);
    var oCompteur;
    var key = "";

    function Compteur() {
        fin = new Date();
        chrono = fin.getTime() - debut.getTime();
        $("#chrono").text(Math.round(chrono / 10) / 100);
        oCompteur = setTimeout(Compteur, 10);
    }

    $("#circuit").mousedown(function (event) {
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
    $("#popup input").click(function () {
        $("#popup").hide();
        $("#circuit").show();
    });

    $("#circuit").mousemove(function (event) {
        event.stopPropagation();
        var offset = $("#circuit").offset();
        delta_x = Math.round(event.pageX - offset.left);
        delta_y = event.pageY - $("#circuit").scrollTop();
        var color = 0;
        if (
            zBitmap[delta_x] != undefined &&
            zBitmap[delta_x][delta_y] != undefined
        ) {
            color = zBitmap[delta_x][delta_y];
        }

        if (!start && color == 1) {
            start = true;
            debut = new Date();
            Compteur();

            pos = new Array(delta_x, delta_y);
            $("#circuit").css("cursor", "url(images/coccinelle.cur), pointer");
        }
        if (start) {
            var deplacement = Math.sqrt(
                Math.pow(Math.abs(pos[0] - delta_x), 2) +
                    Math.pow(Math.abs(pos[1] - delta_y), 2)
            );
            if (deplacement < 400) {
                pos = new Array(delta_x, delta_y);
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
                } else if (color == 3) {
                    clearTimeout(oCompteur);
                    $("#circuit").css("cursor", "auto");
                    fin = new Date();
                    chrono = fin.getTime() - debut.getTime();
                    start = false;
                    $.post(
                        "ajax/key.php",
                        { chrono: chrono / 10 },
                        function (key) {
                            $.post(
                                "ajax/record.php",
                                {
                                    parcours: parcours,
                                    pseudo: pilote,
                                    chrono: chrono / 10,
                                    key: key,
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
                    $("#chrono").text(chrono / 1000);
                    $("#circuit").hide();
                    $("#popup")
                        .show()
                        .find("h1")
                        .html(
                            "Bravo !<br/>Tu as réussi en " +
                                chrono / 1000 +
                                " s"
                        )
                        .css("backgroundColor", "green");
                }
            } else {
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

    $("#jsOK").mousemove(function () {
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

    var getPseudo = function () {
        $("#jsOK").hide();
        $("#getPseudo").show();
        $("#getPseudo input[type=text]").val(pilote).focus();
    };
    $("#getPseudo input[type=button]").click(function () {
        pilote = $.trim($("#getPseudo input[type=text]").val());
        if (pilote != null && pilote.length >= 4 && !pilote.match(ereg)) {
            $("#pseudo").text("[" + pilote + "]");
            $("#getPseudo").hide();
            $("#jsOK").show();
        }
    });

    $("#pseudo").click(function () {
        getPseudo();
    });
    $("#aide").click(function () {
        $("#jsOK").hide();
        $("#consignes").show();
    });
    $("#retour").click(function () {
        $("#consignes").hide();
        $("#jsOK").show();
    });
    $(".changer").click(function () {
        $(this).attr("href", $(this).attr("href") + "&p=" + pilote);
    });
    $.post("ajax/record.php", { parcours: parcours }, function (data) {
        if ($.trim(data) != "") {
            $("#records ol").show().empty().append(data);
            $("#records ul").hide();
        } else {
            $("#records ol").hide();
            $("#records ul").show();
        }
    });

    if (pilote.length < 4 || pilote.match(ereg)) {
        pilote = "Anonyme";
        getPseudo();
    } else {
        $("#getPseudo").hide();
        $("#jsOK").show();
        $("#pseudo").text("[" + pilote + "]");
    }
});
