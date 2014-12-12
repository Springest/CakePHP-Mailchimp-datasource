<?php

App::uses('MailchimpAppModel', 'Mailchimp.Model');

class MailchimpCampaign extends MailchimpAppModel {

	/**
	 * Get the most recent 100 activities for particular list members (open, click, bounce, unsub, abuse, sent to)
	 *
	 *
	 * @param array $emails An array of up to 50 email addresses to get information for OR the "id"(s) for the member returned from listMembers, Webhooks, and Campaigns.
	 * @param array $options
	 * - id
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
	public function listMemberActivity(array $emails, array $options = array()) {
		foreach ($emails as $key => $email) {
			if (is_string($email)) {
				$email = array(
					'email' => $email
				);
			}
			$emails[$key] = $email;
		}
		$defaults = array(
			'id' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;

		return $this->call('reports/member-activity', $options);
	}

	/**
	 * Retrieve all Campaigns Ids a member was sent
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/campaigns-for-email.php
	 *
	 * @param array $emails
	 * @param array $options
	 * @return array
	 */
	public function campaignsForEmail(array $emails, array $options = array()) {
		foreach ($emails as $key => $email) {
			if (is_string($email)) {
				$email = array(
					'email' => $email
				);
			}
			$emails[$key] = $email;
		}
		$options = array(
			'email' => $emails,
			'options' => $options
		);
		return $this->call('helper/campaigns-for-email', $options);
	}

	/**
	 * Get the list of campaigns and their details matching the specified filters
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/list.php
	 *
	 * @param array $filters a hash of filters to apply to this query - all are optional:
	 * - campaign_id
	 * - list_id
	 * - template_id
	 * ...
	 * @param array $options
	 * - $start optional - control paging of campaigns, start results at this campaign #, defaults to 1st page of data  (page 0)
	 * - $limit optional - control paging of campaigns, number of campaigns to return with each call, defaults to 25 (max=1000)
	 * @return array an array containing a count of all matching campaigns and the specific ones for the current page (see Returned Fields for description)
	 */
	public function campaigns($filters = array(), $options = array()) {
		$options['filters'] = $filters;
		return $this->call('campaigns/list', $options);
	}

	/**
	 * Search all campaigns for the specified query terms
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/helper/search-campaigns.php
	 *
	 * @param string $query terms to search on
	 * @param array $options
	 * - integer offset optional the paging offset to use if more than 100 records match
	 * - string snip_start optional by default clear text is returned. To have the match highlighted with something (like a strong HTML tag), <strong>both</strong> this and "snip_end" must be passed. You're on your own to not break the tags - 25 character max.
	 * - string snip_end optional see "snip_start" above.
	 * @return array An array containing the total matches and current results
	 * int total total campaigns matching
	 * array results matching campaigns and snippets
	 * string snippet the matching snippet for the campaign
	 * array campaign the matching campaign's details - will return same data as single campaign from campaigns()
	 */
	public function search($query, array $options = array()) {
		$options['query'] = $query;
		return $this->call('helper/search-campaigns', $options);
	}

	/**
	 * Schedule a campaign to be sent in the future
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/schedule.php
	 *
	 * @param string $cid the id of the campaign to schedule
	 * @param string $scheduleTime the time to schedule the campaign. For A/B Split "schedule" campaigns, the time for Group A - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @param string $scheduleTimeB optional -the time to schedule Group B of an A/B Split "schedule" campaign - in YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return bool true on success
	 */
	public function campaignSchedule(array $options) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId'],
			'scheduleTime' => date('Y-m-d H:i:s'),
			'scheduleTimeB' => null,
		);
		$options += $defaults;
		return $this->call('campaigns/schedule', $options);
	}

	/**
	 * Unschedule a campaign that is scheduled to be sent in the future
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/unschedule.php
	 *
	 * @param array $options
	 * - $cid the id of the campaign to unschedule
	 * @return bool true on success
	 */
	public function campaignUnschedule(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/unschedule', $options);
	}

	/**
	 * Pause an AutoResponder orRSS campaign from sending
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/pause.php
	 *
	 * @param string $cid the id of the campaign to pause
	 * @return bool true on success
	 */
	public function campaignPause(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/pause', $options);
	}

	/**
	 * Resume sending an AutoResponder or RSS campaign
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/resume.php
	 *
	 * @param string $cid the id of the campaign to pause
	 * @return bool true on success
	 */
	public function campaignResume(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/resume', $options);
	}


	/**
	 * Send a given campaign immediately. For RSS campaigns, this will "start" them.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/send.php
	 *
	 * @param string $cid the id of the campaign to send
	 * @return bool true on success
	 */
	public function campaignSend(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/send', $options);
	}

	/**
	 * Send a test of this campaign to the provided email address
	 *
	 * @param array $testEmails an array of email address to receive the test message
	 * @param string $sendType optional by default (null) both formats are sent - "html" or "text" send just that format
	 * @param string $cid the id of the campaign to test
	 * @return bool true on success
	 */
	public function campaignSendTest(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/send-test', $options);
	}

	/**
	 * Allows one to test their segmentation rules before creating a campaign using them
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/segment-test.php
	 *
	 * @param string $listId the list to test segmentation on - get lists using lists()
	 * @param array $options with 2 keys:
	 * @return int total The total number of subscribers matching your segmentation options
	 */
	public function campaignSegmentTest(array $campaignOptions, array $options = array()) {
		$defaults = array(
			'listId' => $this->settings['defaultListId']
		);
		$options += $defaults;
		$options['options'] = $campaignOptions;
		return $this->call('campaigns/segment-test', $options);
	}

	/**
	 * Create a new draft campaign to send. You <strong>can not</strong> have more than 32,000 campaigns in your account.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/create.php
	 *
	 * @param string $type the Campaign Type to create - one of "regular", "plaintext", "absplit", "rss", "trans", "auto"
	 * @param array $options a hash of the standard options for this campaign :     *
	 * @param array $content
	 * @param array $segmentOpts
	 * @return string the ID for the created campaign
	 */
	public function campaignCreate($type, array $campaignOptions, array $content, array $segmentOpts = array(), array $typeOpts = array()) {
		$defaults = array(
			'list_id' => $this->settings['defaultListId']
		);
		$campaignOptions += $defaults;

		$options = array(
			'options' => $campaignOptions
		);
		$options['content'] = $content;
		$options['segment_opts'] = $segmentOpts;
		$options['type_opts'] = $typeOpts;
		return $this->call('campaigns/create', $options);
	}

	/**
	 * Update just about any setting for a campaign that has <em>not</em> been sent. See campaignCreate() for details.
	 *
	 *  Caveats:<br/><ul>
	 *        <li>If you set list_id, all segmentation options will be deleted and must be re-added.</li>
	 *        <li>If you set template_id, you need to follow that up by setting it's 'content'</li>
	 *        <li>If you set segment_opts, you should have tested your options against campaignSegmentTest() as campaignUpdate() will not allow you to set a segment that includes no members.</li></ul>
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/update.php
	 *
	 * @param array $options
	 * - $cid the Campaign Id to update
	 * - string $name the parameter name ( see campaignCreate() ). For items in the <strong>options</strong> array, this will be that parameter's name (subject, from_email, etc.). Additional parameters will be that option name  (content, segment_opts). "type_opts" will be the name of the type - rss, auto, trans, etc.
	 * - mixed $value an appropriate value for the parameter ( see campaignCreate() ). For items in the <strong>options</strong> array, this will be that parameter's value. For additional parameters, this is the same value passed to them.
	 * @return bool true if the update succeeds, otherwise an error will be thrown
	 */
	public function campaignUpdate(array $options) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/update', $options);
	}

	/**
	 * Replicate a campaign.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/replicate.php
	 *
	 * @param string $cid the Campaign Id to replicate
	 * @return string the id of the replicated Campaign created, otherwise an error will be thrown
	 */
	public function campaignReplicate(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/replicate', $options);
	}

	/**
	 * Delete a campaign. Seriously, "poof, gone!" - be careful!
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/delete.php
	 *
	 * @param string $cid the Campaign Id to delete
	 * @return bool true if the delete succeeds, otherwise an error will be thrown
	 */
	public function campaignDelete(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/delete', $options);
	}

	/**
	 * Given a list and a campaign, get all the relevant campaign statistics (opens, bounces, clicks, etc.)
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/summary.php
	 *
	 * @param string $cid the campaign id to pull stats for (can be gathered using campaigns())
	 * @return array struct of the statistics for this campaign
	 * @returnf int syntax_errors Number of email addresses in campaign that had syntactical errors.
	 * @returnf int hard_bounces Number of email addresses in campaign that hard bounced.
	 * @returnf int soft_bounces Number of email addresses in campaign that soft bounced.
	 * @returnf int unsubscribes Number of email addresses in campaign that unsubscribed.
	 * @returnf int abuse_reports Number of email addresses in campaign that reported campaign for abuse.
	 * @returnf int forwards Number of times email was forwarded to a friend.
	 * @returnf int forwards_opens Number of times a forwarded email was opened.
	 * @returnf int opens Number of times the campaign was opened.
	 * @returnf date last_open Date of the last time the email was opened.
	 * @returnf int unique_opens Number of people who opened the campaign.
	 * @returnf int clicks Number of times a link in the campaign was clicked.
	 * @returnf int unique_clicks Number of unique recipient/click pairs for the campaign.
	 * @returnf date last_click Date of the last time a link in the email was clicked.
	 * @returnf int users_who_clicked Number of unique recipients who clicked on a link in the campaign.
	 * @returnf int emails_sent Number of email addresses campaign was sent to.
	 * @returnf array absplit If this was an absplit campaign, stats for the A and B groups will be returned
	 */
	public function campaignStats(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/summary', $options);
	}

	/**
	 * The urls tracked and their click counts for a given campaign.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/click-detail.php
	 *
	 * @param array $options
	 * - cid
	 * @return struct urls will be keys and contain their associated statistics:
	 */
	public function campaignClicks(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/clicks', $options);
	}

	/**
	 * Get an array of the urls being tracked, and their click counts for a given campaign
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/click-detail.php
	 *
	 * @param array $options
	 * - tid: the "tid" for the URL from reports/clicks
	 * @return struct urls will be keys and contain their associated statistics:
	 */
	public function campaignClickDetails($tid, array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['tid'] = $tid;
		$options['opts'] = $filterOptions;
		return $this->call('reports/click-detail', $options);
	}

	/**
	 * Get the top 5 performing email domains for this campaign. Users want more than 5 should use campaign campaignEmailStatsAIM()
	 * or campaignEmailStatsAIMAll() and generate any additional stats they require.
	 *
	 * @section Campaign  Stats
	 *
	 * @example mcapi_campaignEmailDomainPerformance.php
	 *
	 * @param string $cid the campaign id to pull email domain performance for (can be gathered using campaigns())
	 * @return array domains email domains and their associated stats
	 * @returnf string domain Domain name or special "Other" to roll-up stats past 5 domains
	 * @returnf int total_sent Total Email across all domains - this will be the same in every row
	 * @returnf int emails Number of emails sent to this domain
	 * @returnf int bounces Number of bounces
	 * @returnf int opens Number of opens
	 * @returnf int clicks Number of clicks
	 * @returnf int unsubs Number of unsubs
	 * @returnf int delivered Number of deliveries
	 * @returnf int emails_pct Percentage of emails that went to this domain (whole number)
	 * @returnf int bounces_pct Percentage of bounces from this domain (whole number)
	 * @returnf int opens_pct Percentage of opens from this domain (whole number)
	 * @returnf int clicks_pct Percentage of clicks from this domain (whole number)
	 * @returnf int unsubs_pct Percentage of unsubs from this domain (whole number)
	 */
	public function campaignEmailDomainPerformance(array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/domain-performance', $options);
	}

	/**
	 * Get all email addresses the campaign was successfully sent to (ie, no bounces)
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/sent-to.php
	 *
	 * @param string $cid the campaign id to pull members for (can be gathered using campaigns())
	 * @param string $status optional the status to pull - one of 'sent', 'hard' (bounce), or 'soft' (bounce). By default, all records are returned
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array a total of all matching emails and the specific emails for this page
	 * @returnf int total   the total number of members for the campaign and status
	 * @returnf array data  the full campaign member records
	 */
	public function campaignSentTo(array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/domain-performance', $options);
	}

	/**
	 * Get all unsubscribed email addresses for a given campaign
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/unsubscribes.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 1000, upper limit set at 15000
	 * @return array email addresses that unsubscribed from this campaign along with reasons, if given
	 * @return array a total of all unsubscribed emails and the specific emails for this page
	 * @returnf int total   the total number of unsubscribes for the campaign
	 * @returnf array data  the full email addresses that unsubscribed
	 */
	public function campaignUnsubscribes(array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/unsubscribes', $options);
	}

	/**
	 * Get all email addresses that complained about a given campaign
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/abuse.php
	 *
	 * @param string $cid the campaign id to pull abuse reports for (can be gathered using campaigns())
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 500, upper limit set at 1000
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return array reports the abuse reports for this campaign
	 * @returnf string date date/time the abuse report was received and processed
	 * @returnf string email the email address that reported abuse
	 * @returnf string type an internal type generally specifying the orginating mail provider - may not be useful outside of filling report views
	 */
	public function campaignAbuseReports(array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/abuse', $options);
	}

	/**
	 * Retrieve the text presented in our app for how a campaign performed and any advice we may have for you - best
	 * suited for display in customized reports pages. Note: some messages will contain HTML - clean tags as necessary
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/advice.php
	 *
	 * @param string $cid the campaign id to pull advice text for (can be gathered using campaigns())
	 * @return array advice on the campaign's performance
	 * @returnf msg the advice message
	 * @returnf type the "type" of the message. one of: negative, positive, or neutral
	 */
	public function campaignAdvice(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/advice', $options);
	}

	/**
	 * Retrieve the Google Analytics data we've collected for this campaign. Note, requires Google Analytics Add-on to be installed and configured.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/google-analytics.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array analytics we've collected for the passed campaign.
	 * @returnf int visits number of visits
	 * @returnf int pages number of page views
	 * @returnf int new_visits new visits recorded
	 * @returnf int bounces vistors who "bounced" from your site
	 * @returnf double time_on_site the total time visitors spent on your sites
	 * @returnf int goal_conversions number of goals converted
	 * @returnf double goal_value value of conversion in dollars
	 * @returnf double revenue revenue generated by campaign
	 * @returnf int transactions number of transactions tracked
	 * @returnf int ecomm_conversions number Ecommerce transactions tracked
	 * @returnf array goals an array containing goal names and number of conversions
	 */
	public function campaignAnalytics(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/google-analytics', $options);
	}

	/**
	 * Retrieve the countries and number of opens tracked for each. Email address are not returned.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/geo-opens.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array countries an array of countries where opens occurred
	 * @returnf string code The ISO3166 2 digit country code
	 * @returnf string name A version of the country name, if we have it
	 * @returnf int opens The total number of opens that occurred in the country
	 * @returnf bool region_detail Whether or not a subsequent call to campaignGeoOpensByCountry() will return anything
	 */
	public function campaignGeoOpens(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/geo-opens', $options);
	}

	/**
	 * Retrieve the tracked eepurl mentions on Twitter
	 *
	 * @section Campaign  Stats
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @return array stats an array containing tweets, retweets, clicks, and referrer related to using the campaign's eepurl
	 * @returnf array twitter various Twitter related stats
	 */
	public function campaignEepUrlStats(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('reports/eepurl', $options);
	}

	/**
	 * Retrieve the most recent full bounce message for a specific email address on the given campaign.
	 * Messages over 30 days old are subject to being removed
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/bounce-message.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param string $email the email address or unique id of the member to pull a bounce message for.
	 * @return array the full bounce message for this email+campaign along with some extra data.
	 * @returnf string date date/time the bounce was received and processed
	 * @returnf string email the email address that bounced
	 * @returnf string message the entire bounce message received
	 */
	public function campaignBounceMessage($email, array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		if (is_string($email)) {
			$email = array('email' => $email);
		}
		$options['email'] = $email;
		return $this->call('reports/bounce-message', $options);
	}

	/**
	 * Retrieve the full bounce messages for the given campaign. Note that this can return very large amounts
	 * of data depending on how large the campaign was and how much cruft the bounce provider returned. Also,
	 * message over 30 days old are subject to being removed
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/bounce-messages.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 25, upper limit set at 50
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD format in <strong>GMT</strong> (we only store the date, not the time)
	 * @return array bounces the full bounce messages for this campaign
	 * @returnf int total that total number of bounce messages for the campaign
	 * @returnf array data an array containing the data for this page
	 */
	public function campaignBounceMessages(array $filterOptions, array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/bounce-messages', $options);
	}

	/**
	 * Retrieve the Ecommerce Orders tracked by campaignEcommOrderAdd()
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/ecomm-orders.php
	 *
	 * @param string $cid the campaign id to pull bounces for (can be gathered using campaigns())
	 * @param int $start optional for large data sets, the page number to start at - defaults to 1st page of data  (page 0)
	 * @param int $limit optional for large data sets, the number of results to return - defaults to 100, upper limit set at 500
	 * @param string $since optional pull only messages since this time - use YYYY-MM-DD HH:II:SS format in <strong>GMT</strong>
	 * @return array the total matching orders and the specific orders for the requested page
	 * @returnf int total the total matching orders
	 * @returnf array data the actual data for each order being returned
	 */
	public function campaignEcommOrders(array $filterOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['opts'] = $filterOptions;
		return $this->call('reports/ecomm-orders', $options);
	}

	/**
	 * Get the URL to a customized <a href="http://eepurl.com/gKmL" target="_blank">VIP Report</a> for the specified campaign and optionally send an email to someone with links to it. Note subsequent calls will overwrite anything already set for the same campign (eg, the password)
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/reports/share.php
	 *
	 * @param string $cid the campaign id to share a report for (can be gathered using campaigns())
	 * @param array  $opts optional various parameters which can be used to configure the shared report
	 * @return struct Struct containing details for the shared report
	 * @returnf string title The Title of the Campaign being shared
	 * @returnf string url The URL to the shared report
	 * @returnf string secure_url The URL to the shared report, including the password (good for loading in an IFRAME). For non-secure reports, this will not be returned
	 * @returnf string password If secured, the password for the report, otherwise this field will not be returned
	 */
	public function campaignShareReport(array $compaignOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['options'] = $compaignOptions;
		return $this->call('reports/share', $options);
	}

	/**
	 * Get the content (both html and text) for a campaign either as it would appear in the campaign archive or as the raw, original content
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/content.php
	 *
	 * @param string $cid the campaign id to get content for (can be gathered using campaigns())
	 * @param bool   $forArchive optional controls whether we return the Archive version (true) or the Raw version (false), defaults to true
	 * @return struct Struct containing all content for the campaign (see Returned Fields for details
	 * @returnf string html The HTML content used for the campgain with merge tags intact
	 * @returnf string text The Text content used for the campgain with merge tags intact
	 */
	public function campaignContent($compaignOptions = array(), array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		$options['options'] = $compaignOptions;
		return $this->call('campaigns/content', $options);
	}

	/**
	 * Get the HTML template content sections for a campaign. Note that this <strong>will</strong> return very jagged, non-standard results based on the template
	 * a campaign is using. You only want to use this if you want to allow editing template sections in your applicaton.
	 *
	 * @see http://apidocs.mailchimp.com/api/2.0/campaigns/template-content.php
	 *
	 * @param string $cid the campaign id to get content for (can be gathered using campaigns())
	 * @return array array containing all content section for the campaign -
	 */
	public function campaignTemplateContent(array $options = array()) {
		$defaults = array(
			'cid' => $this->settings['defaultCampaignId']
		);
		$options += $defaults;
		return $this->call('campaigns/template-content', $options);
	}

}
