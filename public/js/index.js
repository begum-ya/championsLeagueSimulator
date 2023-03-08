function playAllHandle() {
    $.ajax({
        url: "/championsLeagueSimulator/public/api/play-all",
        type: "post",
        beforeSend: function () {
            $("#loader-wrapper-form").show();
        },
        success: function (response) {
            getStanding();
            getResults();
        },
        complete: function (data) {},
    });
}

function nextWeek() {
    var week =
        document.getElementById("match-select").value !== undefined
            ? document.getElementById("match-select").value
            : 1;
    if (week == document.getElementById("match-select").length) {
        week = 1;
    } else {
        week = parseInt(week) + 1;
    }

    document.getElementById("match-select").value = week;
    getResults();
    updateLeague(week);
}

function updateLeague(week) {
    var url = "/championsLeagueSimulator/public/api/next-week?week=" + week;
    $.ajax({
        url: url,
        type: "post",
        beforeSend: function () {
            $("#loader-wrapper-form").show();
        },
        success: function (data) {
            if (data["success"] == false) {
                getStanding();
            } else {
                var table = document
                    .getElementById("mytable")
                    .getElementsByTagName("tbody")[0];
                $("#mytable tbody").empty();
                data["results"].forEach((element) => {
                    table.insertRow().innerHTML =
                        "<td>" +
                        element["name"] +
                        "</td>" +
                        "<td>" +
                        element["pts"] +
                        "</td>" +
                        "<td>" +
                        week +
                        "</td>" +
                        "<td>" +
                        element["w"] +
                        "</td>" +
                        "<td>" +
                        element["d"] +
                        "</td>" +
                        "<td>" +
                        element["l"] +
                        "</td>" +
                        "<td>" +
                        element["gd"] +
                        "</td>";
                });
            }
        },
        complete: function (data) {
            $("#loader-wrapper-form").hide();
        },
    });
}

function getStanding() {
    $("#loader-wrapper-form").show();
    fetch("/championsLeagueSimulator/public/api/standings")
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            var table = document
                .getElementById("mytable")
                .getElementsByTagName("tbody")[0];
            $("#mytable tbody").empty();
            for (let index = 0; index < data.length; index++) {
                //insert Row
                table.insertRow().innerHTML =
                    "<td>" +
                    data[index]["football_club"]["name"] +
                    "</td>" +
                    "<td>" +
                    data[index]["pts"] +
                    "</td>" +
                    "<td>" +
                    data[index]["p"] +
                    "</td>" +
                    "<td>" +
                    data[index]["w"] +
                    "</td>" +
                    "<td>" +
                    data[index]["d"] +
                    "</td>" +
                    "<td>" +
                    data[index]["l"] +
                    "</td>" +
                    "<td>" +
                    data[index]["gd"] +
                    "</td>";
            }
        })
        .then(function () {
            $("#loader-wrapper-form").hide();
        })
        ["catch"](function () {
            $("#loader-wrapper-form").hide();
        });
}

function getChampions(week) {
    $("#loader-wrapper-form").show();
    $.ajax({
        url: "/championsLeagueSimulator/public/api/get-champion/" + week,
        type: "post",
        beforeSend: function () {
            $("#loader-wrapper-form").show();
        },
        success: function (data) {
            var table = document
                .getElementById("champions-table")
                .getElementsByTagName("tbody")[0];
            $("#champions-table tbody").empty();

            if (data["success"] == true) {
                data["results"].forEach((element) => {
                    table.insertRow().innerHTML =
                        "<td>" +
                        element["name"] +
                        "</td>" +
                        "<td>" +
                        element["avg"] +
                        "</td>";
                });
            }
        },
        complete: function (data) {
            $("#loader-wrapper-form").hide();
        },
    });
}

function getResults() {
    var week =
        document.getElementById("match-select").value !== undefined
            ? document.getElementById("match-select").value
            : document.getElementById("match-select").length;

    $("#loader-wrapper-form").show();
    fetch("/championsLeagueSimulator/public/api/results/".concat(week))
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            document.getElementById("match-select").value = week;

            var table = document
                .getElementById("match-table")
                .getElementsByTagName("tbody")[0];
            $("#match-table tbody").empty();

            data["results"].forEach((element) => {
                away_goal =
                    element["away_football_club_goal_count"] != null
                        ? element["away_football_club_goal_count"]
                        : "";
                home_goal =
                    element["home_football_club_goal_count"] != null
                        ? element["home_football_club_goal_count"]
                        : "";
                table.insertRow().innerHTML =
                    "<td>" +
                    element["home_name"] +
                    "</td>" +
                    "<td>" +
                    home_goal +
                    " - " +
                    away_goal +
                    "</td>" +
                    "<td>" +
                    element["away_name"] +
                    "</td>";
            });
            updateLeague(week);
            getChampions(week);
        })
        .then(function () {
            $("#loader-wrapper-form").hide();
        })
        ["catch"](function () {
            $("#loader-wrapper-form").hide();
        });
}

function resetTable() {
    var week =
        document.getElementById("match-select").value !== undefined
            ? document.getElementById("match-select").value
            : document.getElementById("match-select").length;
    $("#loader-wrapper-form").show();
    fetch("/championsLeagueSimulator/public/api/results/".concat(week))
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            document.getElementById("match-select").value = week;
            var table = document
                .getElementById("match-table")
                .getElementsByTagName("tbody")[0];
            $("#match-table tbody").empty();

            data["results"].forEach((element) => {
                away_goal =
                    element["away_football_club_goal_count"] != null
                        ? element["away_football_club_goal_count"]
                        : "";
                home_goal =
                    element["home_football_club_goal_count"] != null
                        ? element["home_football_club_goal_count"]
                        : "";
                table.insertRow().innerHTML =
                    "<td>" +
                    element["home_name"] +
                    "</td>" +
                    "<td>" +
                    home_goal +
                    " - " +
                    away_goal +
                    "</td>" +
                    "<td>" +
                    element["away_name"] +
                    "</td>";
            });
            getChampions(week);
        })
        .then(function () {
            $("#loader-wrapper-form").hide();
        })
        ["catch"](function () {
            $("#loader-wrapper-form").hide();
        });
}

function resetLeagueHandle() {
    $("#loader-wrapper-form").show();
    $.ajax({
        url: "/championsLeagueSimulator/public/api/reset-league",
        type: "get",
        beforeSend: function () {
            $("#loader-wrapper-form").show();
        },
        success: function (response) {
            getStanding();
            resetTable();
        },
        complete: function (data) {},
    });
}
