<?php

	class ISC_SUBSCRIBE
	{

		public function HandlePage()
		{
			$action = "";
			if(isset($_REQUEST['action'])) {
				$action = isc_strtolower($_REQUEST['action']);
			}

			switch($action) {
				case "subscribe": {
					$this->Subscribe();
					break;
				}
				default: {
					ob_end_clean();
					header(sprintf("Location:%s", $GLOBALS['ShopPath']));
					die();
				}
			}
		}

		/*
		*	Add the visitor to either the bult-in mailing list or to Email Marketer
		*/
		public function Subscribe()
		{
			if(!isset($_POST['check'])) {
				$GLOBALS['SubscriptionHeading'] = GetLang('Oops');
				$GLOBALS['Class'] = "ErrorMessage";
				$GLOBALS['SubscriptionMessage'] = GetLang('NewsletterSpammerVerification');
			}
			else if(isset($_POST['nl_first_name']) && isset($_POST['nl_email'])) {

				$first_name = $_POST['nl_first_name'];
				$email = $_POST['nl_email'];

				// Is Email Marketer integrated or should we just use the built-in list?
				if(GetConfig('MailXMLAPIValid') && GetConfig('UseMailerForNewsletter') && GetConfig('MailNewsletterList') > 0) {
					// Add them to the Email Marketer list
					$GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO'] = GetClass('ISC_ADMIN_SENDSTUDIO');
					$result = $GLOBALS['ISC_CLASS_ADMIN_SENDSTUDIO']->AddSubscriberToNewsletter($first_name, $email);
				}
				else {
					$result = $this->AddSubscriberToNewsletter($first_name, $email);
				}

				if($result['status'] == "success") {
					$GLOBALS['SubscriptionHeading'] = GetLang('NewsletterThanksForSubscribing');
					$GLOBALS['Class'] = "";
					$GLOBALS['SubscriptionMessage'] = $result['message'] . sprintf(" <a href='%s'>%s.</a>", $GLOBALS['ShopPath'], GetLang('Continue'));
				}
				else {
					$GLOBALS['SubscriptionHeading'] = GetLang('Oops');
					$GLOBALS['Class'] = "ErrorMessage";
					$GLOBALS['SubscriptionMessage'] = $result['message'];
				}
			}
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetPageTitle(sprintf("%s - %s", GetConfig('StoreName'), GetLang('NewsletterSubscription')));
			$GLOBALS['ISC_CLASS_TEMPLATE']->SetTemplate("newsletter_subscribe");
			$GLOBALS['ISC_CLASS_TEMPLATE']->ParseTemplate();
		}

		/**
		*	Add a subscriber to the mailing list for the newsletter. Returns an array contaning
		*	status (success/fail) and an optional return message
		*/
		public function AddSubscriberToNewsletter($FirstName, $Email)
		{
			// Is this email address valid?
			if (!is_email_address($Email)) {
				$result = array("status" => "fail",
								"message" => sprintf(GetLang('NewsletterInvalidEmail'), isc_html_escape($Email))
				);

			// Is this person already in the subscribers table?
			} else if ($this->SubscriberExists($Email)) {
				$result = array("status" => "fail",
								"message" => sprintf(GetLang('NewsletterAlreadySubscribed'), isc_html_escape($Email))
				);

			// Else add the subscriber
			} else {
				$NewSubscriber = array(
					"subemail" => $Email,
					"subfirstname" => $FirstName
				);
				$GLOBALS['ISC_CLASS_DB']->InsertQuery("subscribers", $NewSubscriber);

				if($GLOBALS['ISC_CLASS_DB']->Error() == "") {
					// The subscriber was saved
					$result = array("status" => "success",
									"message" => GetLang('NewsletterSubscribedSuccessfully')
					);
				}
				else {
					// Something went wrong with the database
					$result = array("status" => "fail",
									"message" => GetLang('NewsletterSubscribeError')
					);
				}
			}

			return $result;
		}

		/**
		*	Is an email address already in the subscribers table?
		*/
		public function SubscriberExists($Email)
		{
			$query = sprintf("select count(subemail) as subexists from [|PREFIX|]subscribers where subemail='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($Email));
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
			$row = $GLOBALS['ISC_CLASS_DB']->Fetch($result);

			if($row['subexists'] > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}