let platforms = [],
    courses = [],
    blocks = [],
    tasks = [],
    marks = [],
    stage = "platforms";

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
                stage = "platforms";
                $(".tables").css("display", "none");
                DrawModalInfo(data, "Выберите платформы", "platform");
                $(".modalewin").slideDown();
            },
            cache: false,
        });
    });

    $(".modal__submit").on("click", function (e) {
        if (stage == "platforms") ApplyPlatforms(e);
        else if (stage == "courses") ApplyCourses(e);
    });
});

function ApplyPlatforms(event) {
    event.stopPropagation();
    let current_courses = [];
    $('.flexer input[name="platform"]:checked').each((index, platform) => {
        let platform_id = platform.id.slice(8);
        platforms.push(platform_id);
        $.ajax({
            url: `/api/platforms/${platform_id}/courses`,
            method: "GET",
            async: false,
            cache: false,
            success: function (data) {
                current_courses.push(...data);
            },
        });
    });
    stage = "courses";
    DrawModalInfo(current_courses, "Выберите курсы", "course");
}

function ApplyCourses(event) {
    let current_blocks = [];
    $('.flexer input[name="courses"]:checked').each((index, course) => {
        let course_id = course.id.slice(7);
        courses.push(course_id);
        $ajax({
            url: `/api/courses/${course_id}/blocks`,
            method: "GET",
            async: false,
            caches: false,
            success: function (data) {
                current_blocks.push(...data);
            },
        });
    });
    stage = "blocks";
    $(".tables").css("display", "block");
}

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
                        class: ".inmodale label",
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
