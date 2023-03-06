---
Name: exportedworkflow
---
SilverStripe\\Core\\Injector\\Injector:
  ExportedWorkflow:
    class: DNADesign\\Workflow\\Templates\\WorkflowTemplate
    constructor:
	<% with ExportMetaData %>
      - '$ExportWorkflowDefName $ExportDate'
      - 'Exported from $ExportHost on $ExportDate by $ExportUser using SilverStripe versions $ExportVersionFramework'
      - 0.2
      - $ExportRemindDays
      - $ExportSort
	<% end_with %>
    properties:
      structure:
	  <% if ExportUsers %>
        users:
		<% loop ExportUsers %>
          - email: $Email
		<% end_loop %>
	  <% end_if %>
	  <% if ExportGroups %>
        groups:
		<% loop ExportGroups %>
          - title: '$Title'
		<% end_loop %>
	  <% end_if %>
	  <% if ExportActions %>
	  <% loop ExportActions %>
        '$Title':
          type: $ClassName
		  <% if Users %>
          users:
		  <% loop Users %>
            - email: $Email
		  <% end_loop %>
		  <% end_if %>
		  <% if Groups %>
          groups:
		  <% loop Groups %>
            - title: '$Title'
		  <% end_loop %>
		  <% end_if %>
		  <% if Transitions %>
          transitions:
		  <% loop Transitions %>
            - $Title: '$NextAction.Title'
		    <% if Users %>
              users:
			  <% loop Users %>
                - email: $Email
			  <% end_loop %>
		    <% end_if %>
		    <% if Groups %>
              groups:
		    <% loop Groups %>
                - title: '$Title'
		    <% end_loop %>
			<% end_if %>
		  <% end_loop %>
		  <% end_if %>
	  <% end_loop %>
	  <% end_if %>
  DNADesign\\Workflow\\Services\\WorkflowService:
    properties:
      templates:
        - %\$ExportedWorkflow
