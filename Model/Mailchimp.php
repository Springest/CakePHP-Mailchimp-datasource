<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');
App::uses('HttpSocket', 'Network/Http');

class Mailchimp extends MailchimpAppModel {

	/**
	 * Retrieve a list of all MailChimp API Keys for this User
	 *
	 * @section Security Related
	 * @example xml-rpc_apikeyAdd.php
	 * @example mcapi_apikeyAdd.php
	 *
	 * @param string $username Your MailChimp user name
	 * @param string $password Your MailChimp password
	 * @param boolean $expired optional - whether or not to include expired keys, defaults to false
	 * @return array an array of API keys including:
	 * @returnf string apikey The api key that can be used
	 * @returnf string created_at The date the key was created
	 * @returnf string expired_at The date the key was expired
	 */
	public function apiKeys($username, $password, $expired = false) {
		return $this->Mailchimp->apikeys($username, $password, $expired);
	}

	/**
	 * Add an API Key to your account. We will generate a new key for you and return it.
	 *
	 * @section Security Related
	 * @example xml-rpc_apikeyAdd.php
	 *
	 * @param string $username Your MailChimp user name
	 * @param string $password Your MailChimp password
	 * @return string a new API Key that can be immediately used.
	 */
	public function apikeyAdd($username, $password) {
		return $this->Mailchimp->apikeyAdd($username, $password);
	}

	/**
	 * Expire a Specific API Key. Note that if you expire all of your keys, just visit <a href="http://admin.mailchimp.com/account/api" target="_blank">your API dashboard</a>
	 * to create a new one. If you are trying to shut off access to your account for an old developer, change your
	 * MailChimp password, then expire all of the keys they had access to. Note that this takes effect immediately, so make
	 * sure you replace the keys in any working application before expiring them! Consider yourself warned...
	 *
	 * @section Security Related
	 * @example mcapi_apikeyExpire.php
	 * @example xml-rpc_apikeyExpire.php
	 *
	 * @param string $username Your MailChimp user name
	 * @param string $password Your MailChimp password
	 * @return boolean true if it worked, otherwise an error is thrown.
	 */
	public function apikeyExpire($username, $password) {
		return $this->Mailchimp->apikeyExpire($username, $password);
	}

	/**
	 * "Ping" the MailChimp API - a simple method you can call that will return a constant value as long as everything is good. Note
	 * than unlike most all of our methods, we don't throw an Exception if we are having issues. You will simply receive a different
	 * string back that will explain our view on what is going on.
	 *
	 * @section Helper
	 * @example xml-rpc_ping.php
	 *
	 * @return string returns "Everything's Chimpy!" if everything is chimpy, otherwise returns an error message
	 */
	public function ping() {
		return $this->Mailchimp->ping();
	}

	/**
	 * Retrieve lots of account information including payments made, plan info, some account stats, installed modules,
	 * contact info, and more. No private information like Credit Card numbers is available.
	 *
	 * @section Helper
	 *
	 * @param array $exclude optional defaults to nothing for backwards compatibility. Allows controlling which extra arrays are returned since they can slow down calls. Valid keys are "modules", "orders", "rewards-credits", "rewards-inspections", "rewards-referrals", and "rewards-applied". Hint: "rewards-referrals" is typically the culprit. To avoid confusion, if data is excluded, the corresponding key <strong>will not be returned at all</strong>.
	 * @return array containing the details for the account tied to this API Key
	 * string username The Account username
	 * string user_id The Account user unique id (for building some links)
	 * bool is_trial Whether the Account is in Trial mode (can only send campaigns to less than 100 emails)
	 * bool is_approved Whether the Account has been approved for purchases
	 * bool has_activated Whether the Account has been activated
	 * string timezone The timezone for the Account - default is "US/Eastern"
	 * string plan_type Plan Type - "monthly", "payasyougo", or "free"
	 * int plan_low <em>only for Monthly plans</em> - the lower tier for list size
	 * int plan_high <em>only for Monthly plans</em> - the upper tier for list size
	 * string plan_start_date <em>only for Monthly plans</em> - the start date for a monthly plan
	 * int emails_left <em>only for Free and Pay-as-you-go plans</em> emails credits left for the account
	 * bool pending_monthly Whether the account is finishing Pay As You Go credits before switching to a Monthly plan
	 * string first_payment date of first payment
	 * string last_payment date of most recent payment
	 * int times_logged_in total number of times the account has been logged into via the web
	 * string last_login date/time of last login via the web
	 * string affiliate_link Monkey Rewards link for our Affiliate program
	 * array contact Contact details for the account
	 * string fname First Name
	 * string lname Last Name
	 * string email Email Address
	 * string company Company Name
	 * string address1 Address Line 1
	 * string address2 Address Line 2
	 * string city City
	 * string state State or Province
	 * string zip Zip or Postal Code
	 * string country Country name
	 * string url Website URL
	 * string phone Phone number
	 * string fax Fax number
	 * array modules Addons installed in the account
	 * string id An internal module id
	 * string name The module name
	 * string added The date the module was added
	 * array data Any extra data associated with this module as key=>value pairs
	 * array orders Order details for the account
	 * int order_id The order id
	 * string type The order type - either "monthly" or "credits"
	 * double amount The order amount
	 * string date The order date
	 * double credits_used The total credits used
	 * array rewards Rewards details for the account including credits & inspections earned, number of referals, referal details, and rewards used
	 * int referrals_this_month the total number of referrals this month
	 * string notify_on whether or not we notify the user when rewards are earned
	 * string notify_email the email address address used for rewards notifications
	 * array credits Email credits earned:
	 * int this_month credits earned this month
	 * int total_earned credits earned all time
	 * int remaining credits remaining
	 * array inspections Inbox Inspections earned:
	 * int this_month credits earned this month
	 * int total_earned credits earned all time
	 * int remaining credits remaining
	 * array referrals All referrals, including:
	 * string name the name of the account
	 * string email the email address associated with the account
	 * string signup_date the signup date for the account
	 * string type the source for the referral
	 * array applied Applied rewards, including:
	 * int value the number of credits user
	 * string date the date appplied
	 * int order_id the order number credits were applied to
	 * string order_desc the order description
	 */
	function getAccountDetails(array $exclude = array()) {
		return $this->Mailchimp->getAccountDetails($exclude);
	}

	/**
	 * Retrieve all domains verification records for an account
	 *
	 * @section Helper
	 *
	 * @return array records of domains verification has been attempted for
	 * string domain the verified domain
	 * string status the status of the verification - either "verified" or "pending"
	 * string email the email address used for verification
	 */
	public function getVerifiedDomains() {
		return $this->Mailchimp->getVerifiedDomains();
	}


	/**
	 * Get the most recent 100 activities for particular list members (open, click, bounce, unsub, abuse, sent to)
	 *
	 * @section List Related
	 *
	 * @param string $id the list id to connect to. Get by calling lists()
	 * @param array $email_address an array of up to 50 email addresses to get information for OR the "id"(s) for the member returned from listMembers, Webhooks, and Campaigns.
	 * @return array array of data and success/error counts
	 * int success the number of subscribers successfully found on the list
	 * int errors the number of subscribers who were not found on the list
	 * array data an array of arrays where each activity record has:
	 * string action The action name, one of: open, click, bounce, unsub, abuse, sent, queued, ecomm, mandrill_send, mandrill_hard_bounce, mandrill_soft_bounce, mandrill_open, mandrill_click, mandrill_spam, mandrill_unsub, mandrill_reject
	 * string timestamp The date/time of the action
	 * string url For click actions, the url clicked, otherwise this is empty
	 * string type If there's extra bounce, unsub, etc data it will show up here.
	 * string bounce_type For backwards compat, this will exist and be the same data as "type"
	 * string campaign_id The campaign id the action was related to, if it exists - otherwise empty (ie, direct unsub from list)
	 */
	public function listMemberActivity($email_address, $id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listMemberActivity($id, $email_address);
	}

	/**
	 * Get all email addresses that complained about a given campaign
	 *
	 * @section List Related
	 *
	 * @example mcapi_listAbuseReports.php
	 *
	 * @param string $id the list id to pull abuse reports for (can be gathered using lists())
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
	 * @param string $since optional pull only messages since this time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
	 * @return array the total of all reports and the specific reports reports this page
	 * int total the total number of matching abuse reports
	 * array data the actual data for each reports, including:
	 * string date date/time the abuse report was received and processed
	 * string email the email address that reported abuse
	 * string campaign_id the unique id for the campaign that report was made against
	 * string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
	 */
	public function listAbuseReports($start = 0, $limit = 500, $since = null, $id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listAbuseReports($id, $start, $limit, $since);
	}

	/**
	 * Access the Growth History by Month for a given list.
	 *
	 * @section List Related
	 *
	 * @example mcapi_listGrowthHistory.php
	 *
	 * @param string $id the list id to connect to. Get by calling lists()
	 * @return array array of months and growth
	 * string month The Year and Month in question using YYYY-MM format
	 * int existing number of existing subscribers to start the month
	 * int imports number of subscribers imported during the month
	 * int optins number of subscribers who opted-in during the month
	 */
	public function listGrowthHistory($id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listGrowthHistory($id);
	}

	/**
	 * Access up to the previous 180 days of daily detailed aggregated activity stats for a given list
	 *
	 * @section List Related
	 *
	 *
	 * @param string $id the list id to connect to. Get by calling lists()
	 * @return array array of array of daily values, each containing:
	 * string day The day in YYYY-MM-DD
	 * int emails_sent number of emails sent to the list
	 * int unique_opens number of unique opens for the list
	 * int recipient_clicks number of clicks for the list
	 * int hard_bounce number of hard bounces for the list
	 * int soft_bounce number of soft bounces for the list
	 * int abuse_reports number of abuse reports for the list
	 * int subs number of double optin subscribes for the list
	 * int unsubs number of manual unsubscribes for the list
	 * int other_adds number of non-double optin subscribes for the list (manual, API, or import)
	 * int other_removes number of non-manual unsubscribes for the list (deletions, empties, soft-bounce removals)
	 */
	function listActivity($id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listActivity($id);
	}

	/**
	 * Retrieve the locations (countries) that the list's subscribers have been tagged to based on geocoding their IP address
	 *
	 * @section List Related
	 *
	 * @param string $id the list id to connect to. Get by calling lists()
	 * @return array array of locations
	 * string country the country name
	 * string cc the 2 digit country code
	 * double percent the percent of subscribers in the country
	 * double total the total number of subscribers in the country
	 */
	public function listLocations($id = null) {
		if (!$id) {
			$id = $this->settings['defaultListId'];
		}
		return $this->Mailchimp->listLocations($id);
	}

}
