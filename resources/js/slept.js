let platform = [],
    course = [],
    blocks = [],
    tasks = [],
    marks = [],
    stage = "platforms",
    current_page = 0;

$(function () {
    $(".modal").click(function () {
        $(".modal").fadeOut();
    });

    $(".modal__content").click(function (event) {
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
                $(".modal").slideDown();
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
    platform = $('.modal__items input[name="platform"]:checked')[0];
    let platform_id = platform.id.slice(8);
    $.ajax({
        url: `/api/platforms/${platform_id}/courses`,
        method: "GET",
        cache: false,
        success: function (data) {
            current_courses.push(...data);
            stage = "courses";
            DrawModalInfo(current_courses, "Выберите курсы", "course");
        },
    });
}

function ApplyCourses(event) {
    course = $('.modal__items input[name="course"]:checked')[0];
    let course_id = course.id.slice(6);
    stage = "table";
    GetCourseInfo(course_id, 0).then((data) =>
        DrawTable(data["points_max"], data["students"])
    );
}

function GetCourseInfo(course_id, page) {
    return Promise.resolve(
        $.ajax({
            url: `/api/courses/${course_id}/students?page=${page}`,
            method: "GET",
            caches: false,
        })
    );
}

function DrawTable(max_points, students) {
    const table = $("#CourseStudentsTable tbody");
    table.empty();
    $("#CourseStudentsTable").css("display", "table");
    students.forEach((student) => {
        table.append(
            $("<tr>", { class: "student", id: `Student${student.id}` })
                .append($("<td>", { text: student.full_name }))
                .append($("<td>", { text: student.points }))
                .append(
                    $("<td>", {
                        text: Math.round(
                            (parseInt(student.points) / parseInt(max_points)) *
                                100
                        ),
                    })
                )
        );
    });
}

function DrawModalInfo(data, title, prefix) {
    $(".modal__title").text(title);
    $(".modal__items").empty();
    data.forEach((el) => {
        $(".modal__items").append(
            $("<div>", { class: "platforms" })
                .append(
                    $("<input>", {
                        type: "radio",
                        id: prefix + el.id,
                        name: prefix,
                        class: ".modal__content label",
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
