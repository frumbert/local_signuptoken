// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Js file to handle signuptoken
 *
 * @package     local_signuptoken
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/ajax", "core/url"], function($, ajax, url) {
    return {
        init: function($params) {
            $(document).ready(function() {

                function hey(title, time = 2000) {
                    const id = "local_signuptoken_copy";
                    const toast = $(
                        '<div id="' +
                        id +
                        '">' +
                        M.util.get_string("copied", "local_signuptoken") +
                        "<div>"
                    ).get(0);
                    document.querySelector("body").appendChild(toast);
                    toast.classList.add("show");
                    setTimeout(function() {
                        toast.classList.add("fade");
                        setTimeout(function() {
                            toast.classList.remove("fade");
                            setTimeout(function() {
                                toast.remove();
                            }, time);
                        }, time);
                    });
                }

                $(document).on("click", ".st_primary_copy_btn", function(event) {
                    event.preventDefault();

                    var parent = $(this).parent().parent();

                    parent = parent.find(".st_copy");

                    if (parent.attr("id") == "id_st_token") {
                        var copyText = parent.val();
                    } else {
                        var copyText = parent.text();
                    }

                    var temp = document.createElement("textarea");
                    temp.textContent = copyText;

                    document.body.appendChild(temp);
                    var selection = document.getSelection();
                    var range = document.createRange();
                    //  range.selectNodeContents(textarea);
                    range.selectNode(temp);
                    selection.removeAllRanges();
                    selection.addRange(range);

                    document.execCommand("copy");

                    temp.remove();
                    hey("Title", 200);
                });

                function add_new_service_in_select(element, name, id) {
                    $(element + "option:selected").removeAttr("selected");
                    $(element).append(
                        '<option value="' + id + '" selected> ' + name + " </option>"
                    );
                }

                function add_new_token_in_select(element, token, id) {
                    $(element + "option:selected").removeAttr("selected");
                    $(element).append(
                        '<option data-id="' +
                        id +
                        '" value="' +
                        token +
                        '" selected> ' +
                        token +
                        " </option>"
                    );
                }

                function link_web_service(
                    service_id,
                    token,
                    common_errr_fld,
                    common_success_fld
                ) {
                    $("body").css("cursor", "progress");
                    $("#eb_common_err").css("display", "none");

                    var promises = ajax.call([{
                        methodname: "st_link_service",
                        args: { service_id: service_id, token: token },
                    }, ]);

                    promises[0]
                        .done(function(response) {
                            $("body").css("cursor", "default");
                            if (response.status) {
                                $(common_success_fld).text(response.msg);
                                $(common_success_fld).css("display", "block");
                            } else {
                                $(common_errr_fld).text(response.msg);
                                $(common_success_fld).css("display", "block");
                            }

                            return response;
                        })
                        .fail(function(response) {
                            $("body").css("cursor", "default");
                            return 0;
                        }); //promise end
                }

                function create_web_service(
                    web_service_name,
                    user_id,
                    service_select_fld,
                    common_errr_fld,
                    is_mform
                ) {
                    $("body").css("cursor", "progress");
                    $("#st_common_err").css("display", "none");

                    $("#id_st_token option:selected").removeAttr("selected");

                    $('#id_st_token option[value=""]').attr("selected", true);

                    var promises = ajax.call([{
                        methodname: "st_create_service",
                        args: { web_service_name: web_service_name, user_id: user_id },
                    }, ]);

                    var validation_error = 0;

                    if (!validation_error) {
                        promises[0]
                            .done(function(response) {
                                $("body").css("cursor", "default");
                                if (response.status) {
                                    add_new_service_in_select(
                                        service_select_fld,
                                        web_service_name,
                                        response.service_id
                                    );
                                    add_new_token_in_select(
                                        "#id_eb_token",
                                        response.token,
                                        response.service_id
                                    );
                                } else {
                                    $("#eb_common_err").css("display", "block");
                                    $(common_errr_fld).text(response.msg);
                                }

                                return response;
                            })
                            .fail(function(response) {
                                $("body").css("cursor", "default");
                                return 0;
                            }); //promise end
                    }
                }

                $("#id_st_mform_create_service").click(function(event) {
                    event.preventDefault();
                    var error = 0;
                    var web_service_name = $("#id_st_service_inp").val();
                    var user_id = $("#id_st_auth_users_list").val();
                    var service_id = $("#id_st_sevice_list").val();
                    var token = $("#id_st_token").val();

                    $(".st_settings_err").remove();
                    $("#st_common_success").css("display", "none");
                    $("#st_common_err").css("display", "none");

                    if (user_id == "") {
                        $("#st_common_err").text(
                            M.util.get_string("st_empty_user_err", "local_signuptoken")
                        );
                        $("#st_common_err").css("display", "block");
                        error = 1;
                    }

                    //If the select box has a value to create the web service the create web service else
                    if (service_id == "create") {
                        if (web_service_name == "") {
                            $("#st_common_err").css("display", "block");
                            $("#st_common_err").text(
                                M.util.get_string("st_empty_name_err", "local_signuptoken")
                            );
                            error = 1;
                        }

                        if (error) {
                            return;
                        }

                        create_web_service(
                            web_service_name,
                            user_id,
                            "#id_st_sevice_list",
                            "#st_common_err",
                            1
                        );
                    } else {
                        if ($("#id_st_token").val() == "") {
                            $("#st_common_err").css("display", "block");
                            $("#st_common_err").text(
                                M.util.get_string("token_empty", "local_signuptoken")
                            );
                            error = 1;
                            return 0;
                        }

                        if (error) {
                            return;
                        }

                        //If select has selected existing web service
                        if (service_id != "") {
                            link_web_service(
                                service_id,
                                token,
                                "#st_common_err",
                                "#st_common_success"
                            );
                        } else {
                            //If the select box has been selected with the placeholder
                            $("#st_common_err").text(
                                M.util.get_string("st_service_select_err", "local_signuptoken")
                            );
                        }
                    }

                });
            });
        }
    }
});
