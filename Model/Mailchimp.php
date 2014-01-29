<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');
App::uses('HttpSocket', 'Network/Http');

class Mailchimp extends MailchimpAppModel {

	/**
	 * "Ping" the MailChimp API - a simple method you can call that will return a constant value as long as everything is good. Note
	 * than unlike most all of our methods, we don't throw an Exception if we are having issues. You will simply receive a different
	 * string back that will explain our view on what is going on.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/ping.php

	 * @return string returns "Everything's Chimpy!" if everything is chimpy, otherwise returns an error message
	 */
	public function ping() {
		return $this->call('helper/ping');
	}

	/**
	 * Mailchimp::generateText()
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/generate-text.php
	 *
	 * @param mixed $type
	 * @param mixed $content
	 * @return array
	 */
	public function generateText($type, array $content) {
		$options = array(
			'type' => $type,
			'content' => $content
		);
		return $this->call('helper/generate-text', $options);
	}

	/**
	 * Mailchimp::generateText()
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/inline-css.php
	 *
	 * @param mixed $type
	 * @param mixed $content
	 * @return array
	 */
	public function inlineCss($html, $stripCss = true) {
		$options = array(
			'html' => $html,
			'strip_css' => $stripCss
		);
		return $this->call('helper/inline-css', $options);
	}

	/**
	 * Retrieve lots of account information including payments made, plan info, some account stats, installed modules,
	 * contact info, and more. No private information like Credit Card numbers is available.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/account-details.php
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
	public function getAccountDetails(array $exclude = array()) {
		$options = array(
			'exclude' => $exclude
		);
		return $this->call('helper/account-details', $options);
	}

	/**
	 * Retrieve all domains verification records for an account
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/verified-domains.php
	 *
	 * @return array records of domains verification has been attempted for
	 * string domain the verified domain
	 * string status the status of the verification - either "verified" or "pending"
	 * string email the email address used for verification
	 */
	public function getVerifiedDomains() {
		return $this->call('helper/verified-domains');
	}

	/**
	 * Retrieve all of the lists defined for your user account
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/list.php
	 *
	 * @param array $filters
	 * - list_id
	 * - list_name
	 * ...
	 * @param array $options
	 * - start
	 * - limit
	 * - sortField
	 * - sortDir
	 * @return array
	 */
	public function lists($filters = array(), $options = array()) {
		$options['filters'] = $filters;
		return $this->call('lists/list', $options);
	}

	/**
	 * Get all of the list members for a list that are of a particular status. Are you trying to get a dump including lots of merge
	 * data or specific members of a list? If so, checkout the <a href="/export">Export API</a>
	 *
	 * @param array $options
	 * - id
	 * - status to get members for - one of(subscribed, unsubscribed, <a target="_blank" href="http://eepurl.com/gWOO">cleaned</a>, updated), defaults to subscribed
	 * @param array $filterOptions
	 * - start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * - limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 15000
	 * - sortField
	 * - sortDir
	 * - segment
	 * @return array Array of a the total records match and matching list member data for this page (see Returned Fields for details)
	 * int total the total matching records
	 * array data the data for each member, including:
	 * string email Member email address
	 * date timestamp timestamp of their associated status date (subscribed, unsubscribed, cleaned, or updated) in GMT
	 * string reason For unsubscribes only - the reason collected for the unsubscribe. If populated, one of 'NORMAL','NOSIGNUP','INAPPROPRIATE','SPAM','OTHER'
	 * string reason_text For unsubscribes only - if the reason is OTHER, the text entered.
	 */
	public function listMembers(array $options, array $filterOptions = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId'],
			'status' => 'subscribed',
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('lists/members', $options);
	}

	/**
	 * Search account wide or on a specific list using the specified query terms
	 *
	 * @param string $query terms to search on, <a href="http://kb.mailchimp.com/article/i-cant-find-a-recipient-on-my-list" target="_blank">just like you do in the app</a>
	 * @param array
	 * - id: optional the list id to limit the search to. Get by calling lists()
	 * - offset: optional the paging offset to use if more than 100 records match
	 * @return array An array of both exact matches and partial matches over a full search
	 * array exact_matches
	 * int total total members matching
	 * array members each entry will match the data format for a single member as returned by listMemberInfo()
	 * array full_search
	 * int total total members matching
	 * array members each entry will match the data format for a single member as returned by listMemberInfo()
	 */
	public function search($query, array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId'],
		);
		$options += $defaults;
		$options['query'] = $query;
		return $this->call('helper/search-members', $options);
	}

	/**
	 * Retrieve all List Ids a member is subscribed to.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/lists-for-email.php
	 *
	 * @param string $email the email address to check OR the email "id" returned from listMemberInfo, Webhooks, and Campaigns
	 * @return array An array of list_ids the member is subscribed to.
	 */
	public function listsForEmail($email) {
		if (is_string($email)) {
			$email = array(
				'email' => $email
			);
		}
		$options = array(
			'email' => $email
		);
		return $this->call('helper/lists-for-email', $options);
	}

	/**
	 * Get all email addresses that complained about a given campaign sent to a list.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/abuse-reports.php
	 *
	 * @param array $options
	 * - $id the list id to pull abuse reports for (can be gathered using lists())
	 * - integer $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * - integer $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
	 * - string $since optional pull only messages since this time - 24 hour format in <strong>GMT</strong>, eg "2013-12-30 20:30:00"
	 * @return array the total of all reports and the specific reports reports this page
	 * int total the total number of matching abuse reports
	 * array data the actual data for each reports, including:
	 * string date date/time the abuse report was received and processed
	 * string email the email address that reported abuse
	 * string campaign_id the unique id for the campaign that report was made against
	 * string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
	 */
	public function listAbuseReports(array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId']
		);
		$options += $defaults;
		return $this->call('lists/abuse-reports', $options);
	}

	/**
	 * Access the Growth History by Month for a given list.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/growth-history.php
	 *
	 * @param array $options
	 * - $id the list id to connect to. Get by calling lists()
	 * @return array array of months and growth
	 * string month The Year and Month in question using YYYY-MM format
	 * int existing number of existing subscribers to start the month
	 * int imports number of subscribers imported during the month
	 * int optins number of subscribers who opted-in during the month
	 */
	public function listGrowthHistory(array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId']
		);
		$options += $defaults;
		return $this->call('lists/growth-history', $options);
	}

	/**
	 * Access up to the previous 180 days of daily detailed aggregated activity stats for a given list
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/activity.php
	 *
	 * @param array $options
	 * -  $id the list id to connect to. Get by calling lists()
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
	public function listActivity(array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId']
		);
		$options += $defaults;
		return $this->call('lists/activity', $options);
	}

	/**
	 * Retrieve the locations (countries) that the list's subscribers have been tagged to based on geocoding their IP address
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/lists/locations.php
	 *
	 * @param string $id the list id to connect to. Get by calling lists()
	 * @return array array of locations
	 * string country the country name
	 * string cc the 2 digit country code
	 * double percent the percent of subscribers in the country
	 * double total the total number of subscribers in the country
	 */
	public function listLocations(array $options = array()) {
		$defaults = array(
			'id' => $this->settings['defaultListId']
		);
		$options += $defaults;
		return $this->call('lists/locations', $options);
	}

}
