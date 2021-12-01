$(".modalewin").hide();

let platforms = [],
    courses = [],
    blocks = [],
    tasks = [],
    marks = [];

$(function () {
    $(".modalewin").click(function () {
        $(".modalewin").fadeOut();
    });

    $(".inmodale").click(function (event) {
        event.stopPropagation();
    });

    $("#ChoosePlatform").on("click", function (event) {
        $.ajax({
            url: "/api/platforms",
            method: "GET",
            success: function (data) {
                DrawModalInfo(data, "Выберите платформы", "platform");
                $(".modalewin").slideDown();
            },
            cache: false,
        });
    });

    $(".modal__submit").on("click", function (event) {
        // В API сделать сортировку по platforms/id/courses - курсы платформы
        event.stopPropagation();
        let current_courses = [];
        $('.flexer input[name="platform"]:checked').each((index, platform) => {
            let platform_id = platform.id.slice(8);
            console.log(platform_id);
            platforms.push(platform_id);
            $.ajax({
                url: "/api/courses/" + platform_id,
                method: "GET",
                cache: false,
                success: function (data) {
                    current_courses.push(data);
                },
            });
        });
        console.log(current_courses);
        DrawModalInfo(current_courses, 'Выберите курсы', 'course');
    });

    function DrawModalInfo(data, title, prefix) {
        $(".modal__title").text(title);
        $(".flexer").empty();
        data.forEach((el) => {
            $(".flexer").append(
                $("<div>", { class: "platforms" })
                    .append(
                        $("<input>", {
                            type: "checkbox",
                            id: prefix + el.id,
                            name: prefix,
                        })
                    )
                    .append(
                        $("<label>", {
                            for: prefix + el.id,
                            text: el.name,
                        })
                    )
            );
        });
    }
});
