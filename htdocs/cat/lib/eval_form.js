$(function() {
	if ( $( "#eval_form" ).length ) {
		var $form_data_on_page_load = {}; // We'll use this later to decide if we're going to submit any data or not

		function select_in_question(parentid,section_number,question_number,value) {
			//console.log("Section_number: "+section_number+"\tQuestion_number: "+question_number+"\tValue: "+value);
			
			var optionid = "#"+section_number+"_"+question_number+"_"+value;
			
			// Animate color
			$("#"+parentid).children(optionid).animate({backgroundColor: 'rgba(0, 255, 0, 1)'},200);
			$("#"+parentid).children(":not("+optionid+")").animate({backgroundColor: 'rgba(0, 255, 0, 0)'},300);
			
			
			// Update selected marker
			$("#"+parentid).children(":not("+optionid+")").removeClass("selected");
			$("#"+parentid).children(optionid).addClass("selected");
		}
		function are_all_selected() {
			var has_selected = [];
			var ids = [];
			$(".eval_form_section").each(function(section_number){
				///console.log(this);
				has_selected[section_number] = [];
				ids[section_number] = [];
				$(this).children().children().each(function(question_number){
					//console.log(section_number+" "+question_number);
					//console.log(this);
					has_selected[section_number][question_number] = false;
					ids[section_number][question_number] = $(this).attr('id');
					$(this).children(".selected").each(function(){
						has_selected[section_number][question_number] = true;
						
					});
					//console.log(section_number+","+question_number+":"+has_selected[section_number][question_number]);
					
				});
			});
			
			// Process invalid questions or return true
			var retval = true;
			$.each(has_selected,function(section_number,questions) {
				$.each(questions,function(question_number,value) {
					retval = (value ? retval : value);
					if(!value) {
						var id = ids[section_number][question_number];
						$("#"+id).children(".option").animate({backgroundColor: 'rgba(255, 200 , 200, 1)'},300); // Fade row to red
						var msg_txt = '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
						msg_txt += "<strong>ERROR:</strong> Please select a box for each criteria. Errors are marked in red.";
						$("#msg_before_form").html(msg_txt).fadeIn();
						$("#msg_after_form").html(msg_txt).fadeIn();
						
						
					}
				});
			});
			return retval;
		}

		/* 
		 * eval_form_to_json
		 *	returns JSONified string holding the value for each question in the form:
		 * 		retval[section_number][question_number] = value;
		 * It is safe to assume that there IS a value set for every question. Run this function only after validation
		 */
		function eval_form_to_json() {
			var selected = []; // Will hold the value for each question; First dimension is the SECTION -- OLD METHOD
			var answers = {};
			$(".eval_form_section").each(function(section_number){
				selected[section_number] = []; // Second dimension is the QUESTION
				$(this).children().children().each(function(question_number){
					selected[section_number][question_number] = false;
					$(this).children(".option").each(function(value){
						if($(this).hasClass("selected")) {
							var qid = $(this).attr('id').split("_")[1];
							var answer = $(this).find(".val").text();
							if(answer!="—") {// Mdash
								answers[qid]=value;
							}
							selected[section_number][question_number] = value; // Third dimension is the VALUE
						}
					});
				});
			});
			
			return answers;
		}
		function kill_error_messages() {
			var msg_txt = '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
			
			$("#msg_before_form").html(msg_txt).fadeOut();
			$("#msg_after_form").html(msg_txt).fadeOut();
		}
		function on_submit_success(msg) {

			kill_error_messages();
			//$("#eval_section_holder").slideUp(2000).fadeOut();
			$("#response_message").fadeIn();
			$("#response_message .panel-body").html(msg.message);
			if(msg.response==200) {
				$("#response_message").removeClass("panel-info").addClass("panel-success");
				$(".option").off("click"); // Disable changing the form
				$("button.show_form").fadeIn();
				$("input").attr("disabled", "disabled");
				$("textarea").attr("disabled", "disabled");
			}
			else {
				$("#response_message").removeClass("panel-info").addClass("panel-warning");
			}
			$('html,body').animate({
				scrollTop: $("#response_message").offset().top - 10
			});
		}
		$(document).ready(function() {
			
			
			$("#eval_form").submit(function(e) {
			
				e.preventDefault();
				kill_error_messages();
				if(are_all_selected()) {
					var $json_data = {};

					$json_data.responses = eval_form_to_json(); // Grab response data
					$json_data.circuit_rider_id = circuit_rider.id; // Pick circuit rider ID
					$json_data.date_recorded = $("input#date_recorded").val();
					$json_data.comments = {};
					$json_data.community_id = community.id;
					$json_data.deletes = [];
					$("#eval_section_holder .section table tbody tr").each(function(rownum,row) {
						$(row).children("td").children("textarea").each(function(k,v) {
							var qid = $(this).attr('id').split("_")[1];
							$json_data["comments"][qid] = $(v).val();
						});
					});
					
					$("#eval_form").children('input[name="delete[]"]').each(function(k,v) {
						//var row_name = $(v).attr("name").slice(0,$(v).attr("name").length-2);
						$json_data["deletes"].push(($(v).val()));
					});
					// If both the comment and the response stayed the same, remove from data
					$.each($json_data.comments,function(key,value) {
						var keep_it=false;
						if($json_data.responses[key]!=$form_data_on_page_load.responses[key])
							keep_it = true;
						if($json_data.comments[key]!=$form_data_on_page_load.comments[key])
							keep_it = true;
						//console.log(key+" keep: "+keep_it);
						if(!keep_it) {
							delete $json_data.comments[key];
							delete $json_data.responses[key]
						}
						//console.log("Now\t"+key+":"+$json_data.responses[key]);
						//console.log("Then\t"+key+":"+$form_data_on_page_load.responses[key]);
					});
					console.log($json_data.deletes);
					console.log("Submitting form");
					
					$.ajax({
						type: "POST",
						url: "action.php?p="+form_target,
						data: $json_data,
						success: function(msg) {on_submit_success(msg);},
						complete: function(msg) {console.log(msg)},
						dataType: "json",
					});
				}
				else {
					console.log("First clear up errors");
				}
				
				return false;
			});
			$(".option").click(function(e) {
				var parentid = $(this).parent().attr('id'),
					section_number = (parentid.split("_")[0]),
					question_number = (parentid.split("_")[1]),
					value = $(this).children(".val").html();
				select_in_question(parentid,section_number,question_number,value);
			});
			$(".ni").click(function(e) {
				var parentid = $(this).parent().attr('id'),
						section_number = (parentid.split("_")[0]),
						question_id = (parentid.split("_")[1]),
						value = $(this).children(".val").html();
				//var name = $(this).parent().parent().children("td").children("input").val();
				//var name = $(this).parent().parent().children("td").children("input").val();
				$("#eval_form").append('<input type="hidden" name="delete[]" value="'+question_id+'">');
			});
			
			// Now see if we're supposed to check any boxes
			if (typeof click_boxes !== 'undefined') {
				for (i in click_boxes) {
					var id = click_boxes[i];
					$("#"+id).click();
					
				}
			}
			

			$form_data_on_page_load.responses = eval_form_to_json(); // Grab response data
			$form_data_on_page_load.circuit_rider_id = circuit_rider.id; // Pick circuit rider ID
			$form_data_on_page_load.date_recorded = $("input#date_recorded").val();
			$form_data_on_page_load.comments = {};
			$form_data_on_page_load.community_id = community.id;
			$("#eval_section_holder .section table tbody tr").each(function(rownum,row) {
				$(row).children("td").children("textarea").each(function(k,v) {
					var qid = $(this).attr('id').split("_")[1];
					$form_data_on_page_load["comments"][qid] = $(v).val();
				});
			});
		
			
		});

	} // end if form exists
	

});