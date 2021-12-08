let platform = [],
    course = [],
    blocks = [],
    tasks = [],
    marks = [],
    stage = "platforms",
    current_page = 0;

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
    platform = $('.flexer input[name="platform"]:checked')[0];
    let platform_id = platform.id.slice(8);
    $.ajax({
        url: `/api/platforms/${platform_id}/courses`,
        method: "GET",
        async: false,
        cache: false,
        success: function (data) {
            current_courses.push(...data);
        },
    });
    stage = "courses";
    DrawModalInfo(current_courses, "Выберите курсы", "course");
}

function ApplyCourses(event) {
    course = $('.flexer input[name="courses"]:checked')[0];
    let course_id = course.id.slice(7);
    stage = "table";
    let course_info = GetCourseInfo(course_id, 0);
    DrawTable(course_info["points_max"], course_info["students"]);
}

function GetCourseInfo(course_id, page) {
    let result = null;
    $ajax({
        url: `/api/courses/${course_id}/students?page=${page}`,
        method: "GET",
        caches: false,
        async: false,
        success: function (data) {
            result = data;
        },
    });
    return result;
}

function DrawTable(max_points, students) {
    const table = $("#CourseStudentsTable tbody");
    table.empty();
    students.forEach((student) => {
        table.append(
            $("<tr>", { class: "student", id: `Student${student.id}` })
                .append($("<td>", { text: student.full_name }))
                .append($("<td>", { text: student.points }))
                .append(
                    $("<td>", {
                        text:
                            (parseInt(student.points) / parseInt(max_points)) *
                            100,
                    })
                )
        );
    });
}

function DrawModalInfo(data, title, prefix) {
    $(".modal__title").text(title);
    $(".flexer").empty();
    data.forEach((el) => {
        $(".flexer").append(
            $("<div>", { class: "platforms" })
                .append(
                    $("<input>", {
                        type: "radio",
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
