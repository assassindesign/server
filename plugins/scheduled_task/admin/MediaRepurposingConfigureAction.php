<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingConfigureAction extends KalturaApplicationPlugin
{	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}



	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$mediaRepurposingId = $this->_getParam('media_repurposing_id');
		$partnerId = $this->_getParam('new_partner_id');
		$filterTypeEngine = $this->_getParam('new_media_repurposing_filter_type');
		$filterType = MediaRepurposingUtils::filterFactory($filterTypeEngine);
		$action->view->formValid = false;

		try
		{
			if ($request->isPost())
			{
				$formData = $request->getPost();
				$filterType = $formData['filterTypeStr'];
				$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType);
				$valid = $this->processForm($mediaRepurposingForm, $formData, $filterTypeEngine, $mediaRepurposingId);
				$action->view->formValid = $valid;

			}
			else
			{
				if (!is_null($mediaRepurposingId))
				{
					$mr = MediaRepurposingUtils::getMrById($partnerId, $mediaRepurposingId);
					$filterEngineType = $mr->objectFilterEngineType;
					$filterType = MediaRepurposingUtils::filterFactory($filterEngineType);

					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType, $mediaRepurposingId);
					$mediaRepurposingForm->populateFromObject($mr, false);
				}
				else
				{
					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType, null);
					$mediaRepurposingForm->getElement('partnerId')->setValue($partnerId);
					$mediaRepurposingForm->getElement('filterTypeStr')->setValue($filterType);
				}
			}
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $mediaRepurposingForm;
	}



	private function buildTasksArray($tasksData)
	{
		$taskArray = array();
		$tasksData = json_decode($tasksData);

		foreach ($tasksData as $task) {
			$taskArray[] = $this->buildTask($task);
			$taskArray[] = $task->taskTimeToNext;
		}
		return $taskArray;
	}

	private function buildTask($taskObj)
	{
		$typeInt = constant("Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::". $taskObj->type);
		$objectTask = MediaRepurposingUtils::objectTaskFactory($typeInt);
		$objectTask->relatedObjects = $taskObj->id; //as the scheduleTask Id of this task
		$objectTask->stopProcessingOnError = 0; //as default value
		MediaRepurposingUtils::addParamToObjectTask($objectTask, $taskObj->taskData);
		return $objectTask;
	}



	
	private function processForm(Form_MediaRepurposingConfigure $form, $formData, $filterTypeEngine, $mediaRepurposingId = null)
	{
		$name = $formData['media_repurposing_name'];
		$maxEntriesAllowed = $formData['max_entries_allowed'];
		

		$filter = $form->getFilterFromData($formData);
		$taskArray = $this->buildTasksArray($formData['TasksData']);

		KalturaLog::debug("Got the following Data from the Configure Form:");
		KalturaLog::debug(print_r($formData, true));

		if ($form->isValid($formData))
		{
			$partnerId = $formData['partnerId'];
			if (!$mediaRepurposingId)
				MediaRepurposingUtils::createNewMr($name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed);
			else
				MediaRepurposingUtils::UpdateMr($mediaRepurposingId, $name, $filterTypeEngine, $filter, $taskArray, $partnerId, $maxEntriesAllowed);
			return true;
		}
		else
		{
			KalturaLog::info('Form was not valid - keep the form open for changing');
			$formData['generalTitle'] = 1; // mark as return from error
			$form->populate($formData);
			return false;
		}
	}

}

