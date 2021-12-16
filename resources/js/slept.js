let platform = [],
    course = [],
    blocks = [],
    stage = "platforms",
    current_page = 0,
    max_page = 0;

$(function () {
    $(".modal").click(function () {
        $(".modal").fadeOut();
        $("body").css("overflow", "auto");
    });

    $(".modal__content").click(function (event) {
        event.stopPropagation();
    });

    $("#ChoosePlatform").on("click", function (event) {
        let promise = Promise.resolve(
            $.ajax({
                url: "/api/platforms",
                method: "GET",
                cache: false,
            })
        );
        stage = "platforms";
        $("body").css("overflow", "hidden");
        $(".tables").css("display", "none");
        $(".norm_class").css("display", "none");
        $(".modal").slideDown();
        DrawModalInfo(promise, "Выберите платформы", "platform");
    });

    $(".modal__submit").on("click", function (e) {
        if (stage == "platforms") ApplyPlatforms(e);
        else if (stage == "courses") ApplyCourses(e);
    });

    $(".norm_class").on("click", function (e) {
        if (e.target.classList.contains("fir")) current_page--;
        if (e.target.classList.contains("sec")) current_page++;
        let loader = GetLoader();
        CheckPaginationButtons();
        $("#CourseStudentsTable tbody").append(loader);
        GetCourseInfo(course.id.slice(6), current_page).then((data) => {
            DrawTable(data["points_max"], data["students"]);
        });
    });

    $("#CourseStudentsTable").on("click", "tr", function (e) {
        let course_id = course.id.slice(6);
        let student_id = e.target.closest("tr").id.slice(7);
        ShowCourseBlocks(course_id, student_id);
    });

    $(".button-back").on("click", function (e) {
        $(".modal-course").css("display", "none");
        $("body").css("overflow", "auto");
    });

    $(".modal-course__blocks").on(
        "click",
        ".modal-course__block",
        function (e) {
            let block_id = this.id.slice(5);
            if (
                $("#Block" + block_id)
                    .next()
                    .is("div.modal-course__block__info")
            )
                $(".modal-course__block__info").remove();
            else {
                let block = blocks.find((el) => el.id == block_id);
                ShowBlockInfo(block_id, block);
            }
        }
    );
});

function CheckPaginationButtons() {
    if (current_page > 0) $(".mainbutton.fir").css("visibility", "visible");
    else $(".mainbutton.fir").css("visibility", "hidden");
    if (current_page + 1 < max_page)
        $(".mainbutton.sec").css("visibility", "visible");
    else $(".mainbutton.sec").css("visibility", "hidden");
}

function GetLoader() {
    return $("<div>", { class: "loader__container" }).append(
        $("<div>", { class: "loader" })
    );
}

function ShowBlockInfo(block_id, block_info) {
    $(".modal-course__block__info").remove();
    let block = $("#Block" + block_id);
    let tasks_rows = $("<tbody>");
    block_info["tasks"].forEach((task) => {
        tasks_rows.append(
            $("<tr>")
                .append($("<td>", { text: task["name"] }))
                .append($("<td>", { text: task["points_student"] }))
                .append($("<td>", { text: task["points_max"] }))
                .append(
                    $("<td>", {
                        text: Math.round(
                            (task["points_student"] / task["points_max"]) * 100
                        ),
                    })
                )
        );
    });
    block.after(
        $("<div>", { class: "modal-course__block__info" }).append(
            $("<table>")
                .append(
                    $("<thead>").append(
                        $("<tr>")
                            .append($("<th>", { text: "Задание" }))
                            .append($("<th>", { text: "Баллы" }))
                            .append($("<th>", { text: "Максимум" }))
                            .append($("<th>", { text: "Проценты" }))
                    )
                )
                .append(tasks_rows)
        )
    );
}

function GetCourseBlocks(course_id, student_id = null) {
    return Promise.resolve(
        $.ajax({
            url: `/api/courses/${course_id}/blocks${
                !!student_id ? "?student_id=" + student_id : ""
            }`,
            method: "GET",
        })
    );
}

function GetBlockTasks(block_id, student_id = null) {
    return Promise.resolve(
        $.ajax({
            url: `/api/blocks/${block_id}/tasks${
                !!student_id ? "?student_id=" + student_id : ""
            }`,
            method: "GET",
        })
    );
}

function ShowCourseBlocks(course_id, student_id) {
    $("body").css("overflow", "hidden");
    $(".modal-course").css("display", "block");
    let loader = GetLoader();
    let blocks_section = $(".modal-course .modal-course__blocks");
    blocks_section.empty();
    blocks_section.append(loader);
    $(".modal-course__title h1").text(`${course.getAttribute("data-name")}`);
    let student;
    $.ajax({
        url: `/api/students/${student_id}`,
        method: "GET",
        success: (data) => {
            $(".modal-course__title h1").text(
                `${data.full_name} - ${course.getAttribute("data-name")}`
            );
            student = data;
        },
    });
    GetCourseBlocks(course_id, student_id).then(async function (data) {
        blocks = data;
        data.forEach(
            await async function (block) {
                //block['tasks'] = await GetBlockTasks(block['id'], student_id);
                let points_student = 0;
                block["tasks"].forEach((task) => {
                    points_student += task["points_student"];
                });
                blocks_section.append(
                    $("<button>", {
                        class: "modal-course__block",
                        id: "Block" + block["id"],
                    })
                        .append(
                            $("<div>", {
                                class: "modal-course__progress",
                                style: `right: ${
                                    100 -
                                    (points_student / block["points_max"]) * 100
                                }%;`,
                            })
                        )
                        .append(
                            $("<div>", {
                                class: "modal-course__block__content",
                                text: block["name"],
                            })
                        )
                );
            }
        );
        loader.remove();
    });
}

function ApplyPlatforms(event) {
    event.stopPropagation();
    platform = $('.modal__items input[name="platform"]:checked')[0];
    let platform_id = platform.id.slice(8);
    let promise = GetPlatformInfo(platform_id);
    DrawModalInfo(promise, "Выберите курсы", "course");
    stage = "courses";
}

function GetPlatformInfo(platform_id) {
    return Promise.resolve(
        $.ajax({
            url: `/api/platforms/${platform_id}/courses`,
            method: "GET",
            cache: false,
        })
    );
}

function ApplyCourses(event) {
    event.stopPropagation();
    course = $('.modal__items input[name="course"]:checked')[0];
    let course_id = course.id.slice(6);
    stage = "table";
    let loader = GetLoader();
    $(".modal__items").append(loader);
    GetCourseInfo(course_id, 0).then((data) => {
        max_page = Math.ceil(data["students_count"] / 50);
        CheckPaginationButtons();
        DrawTable(data["points_max"], data["students"]);
        $(".norm_class").css("display", "flex");
        loader.remove();
        $(".modal").fadeOut();
        $("body").css("overflow", "auto");
        setTimeout(
            $("#CourseStudentsTableName")[0].scrollIntoView({
                behavior: "smooth",
                block: "start",
            }),
            1000
        );
    });
}

function GetCourseInfo(course_id, page) {
    return Promise.resolve(
        $.ajax({
            url: `/api/courses/${course_id}/students?page=${page}`,
            method: "GET",
        })
    );
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
                        text: Math.round(
                            (parseInt(student.points) / parseInt(max_points)) *
                                100
                        ),
                    })
                )
        );
    });
    $("#CourseStudentsTable").css("display", "table");
    if ($("#CourseStudentsTableName").length == 0)
        $("#CourseStudentsTable").before(
            $("<h1>", {
                text: course.getAttribute("data-name"),
                id: "CourseStudentsTableName",
                class: "table__name",
            })
        );
    else $("#CourseStudentsTableName").text(course.getAttribute("data-name"));
}

function DrawModalInfo(data_promise, title, prefix) {
    $(".modal__title").text(title);
    $(".modal__items").empty();
    let loader = GetLoader();
    $(".modal__items").append(loader);
    data_promise.then((data) => {
        data.forEach((el) => {
            $(".modal__items").append(
                $("<div>", { class: "platforms" })
                    .append(
                        $("<input>", {
                            type: "radio",
                            id: prefix + el.id,
                            name: prefix,
                            class: ".modal__content label",
                            "data-name": el.name,
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
        loader.remove();
    });
}
