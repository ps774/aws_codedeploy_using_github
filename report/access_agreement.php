<?php
include_once('php/db.php');
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
$_SESSION['nav-active'] = $_SERVER['REQUEST_URI'];

?>

<html>
<head>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style>
		.s12{
		    font-size: 10px;
			padding: 0 4px;
		}
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Popsixle Access Agreement</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>			
  
  <div class="p-3 mb-2 card base-card bg-light border text-white bg-danger" id="error_block" style="display:none;">
  </div>
  
  <div class="p-3 mb-2  card base-card bg-light border text-white bg-success" id="success_block" style="display:none;">
  </div>

			
  <div class="mt-3  card base-card bg-light border p-3">
    <h4 class="text-center">Access Agreement</h4>
    <p class="s3">Last updated: 8/04/2022<br></p>
    <p class="s5"><a name="_bef7u94osacl"></a></p>
    <p class="s7"><span class="s8">This Access Agreement (the “<b>Agreement</b>”) is entered into by and between Attention Exchange Inc. (dba as Popsixle), a Delaware corporation (“<b>Popsixle</b>”), and you, and if you are accessing the Platform (as defined below) on behalf of, or in relation to, your employer, or another company, such entity (collectively, the “<b>Client</b>”) (each a “<b>Party</b>”, collectively, the “<b>Parties</b>”).</span></p>
    <p class="s7"><span class="s8">This Agreement is made as of the date that you check the “I agree to the Access Agreement” box on the pop6serve account creation page and submit this form (the “<b>Effective Date</b>”).</span></p>
    <ol type="1">

      <li class="s7"><span class="s8"><b>LICENSE.</b></span></li>
      </br>
      <ol type="a">
        <li class="s7"><span class="s8">Subject to the terms and conditions of this Agreement, Popsixle’s <a href="https://popsixle.com/policies/terms-of-service" target=_blank>Terms of Service</a>, and its <a href="https://popsixle.com/policies/privacy-policy" target=_blank>Privacy Policy</a>, Popsixle grants to Client during the Term, a non-transferable, non-exclusive right, without right to sublicense, to access and use its proprietary software as a service and related platform made available to the Client by Popsixle (collectively, the “<b>Platform</b>”), as updated or revised by Popsixle from time to time. All rights not expressly granted herein are retained by Popsixle.  In the event of a conflict between the terms of this Agreement, the TOS, and/or the Privacy Policy, the order of precedence shall be (in descending order) the Privacy Policy, then the TOS, then this Agreement.</span></li>
        </br>
        <li class="s7"><span class="s8">Popsixle does not guarantee, represent or warrant that access to the Platform will be uninterrupted or error-free, and Popsixle does not guarantee that Client will be able to access or use all or any of the system features at all times. Popsixle may suspend the Platform, in whole or in part, at any time.</span></li>
      </ol>
      </br>
      <li class="s7"><span class="s8"><b>FEES.</b></span></li>
      </br>
      <ol type="a">
        <li class="s7"><span class="s8">Fees, if any, are due and payable as set forth below and in an invoice, payment instructions, or similar mechanism for the access to the Platform (each, an “<b>Invoice</b>”) that sets forth, among other things, the Platform access scope, the length of the access, the fees associated therewith (the “<b>Fees</b>”), and certain additional terms applicable to the Platform. Fees and other charges, if any, do not include federal, local, foreign, sales, transaction, use or value added taxes (“<b>Taxes</b>”) now or hereafter levied, all of which shall be Client’s responsibility. If Popsixle is required to pay Taxes on Client’s behalf, Popsixle shall invoice Client for such Taxes, and Client shall reimburse Popsixle for such amounts in accordance with this Agreement.</span></li>
        </br>
        <li class="s7"><span class="s8">Popsixle will invoice Client for Fees, if any, on the schedule set forth in the applicable Invoice. Unless otherwise specified in an Invoice, Popsixle will invoice Client monthly, with payment being no later than thirty (30) days after receipt by Client. In addition to any other remedies, late payment charges may be assessed on overdue amounts at the lesser of five percent (5%) per month, or the highest rate allowed by law. All Fees due, paid or incurred are non-refundable, even if the Agreement is terminated early.</span></li>
      </ol>
      </br>
      <li class="s7"><span class="s8"><b>RESTRICTIONS.</b></span></li>
      </br>
      <ol type="a">
        <li class="s7"><span class="s8">Except as otherwise specifically permitted in this Agreement, neither the Client nor any of its users may: (1) modify or create any derivative works of the Platform, including translation or localization; (2) access or copy the Platform except as provided in this Agreement or elsewhere in writing by Popsixle; (3) sublicense or permit use of the Platform by any persons not associated with the Client and authorized as a user; (4) reverse engineer, decompile, or disassemble or otherwise attempt to derive the source code for the Platform; (5) redistribute, encumber, publicly display, sell, rent, lease, sublicense, use the Platform in a timesharing or service bureau arrangement, or otherwise transfer rights to the Platform; or (6) remove or alter any trademark, logo, copyright or other proprietary notices, legends, symbols or labels in the Platform.</span></li>
        </br>
          <ol type="i">
            <li class="s7"><span class="s8">Notwithstanding the foregoing, Client shall be permitted, in its sole discretion, and at its sole risk, to use reports, documents, analytics, or other materials generated by or through the Platform (collectively, "<b>Reports</b>") for Client’s general business purposes.  </span></li>
          </ol>
          </br>
        <li class="s7"><span class="s8">Neither Client nor its users are permitted to distribute, upload, transmit, store, make available or otherwise publish or process through the Platform any information or materials provided or submitted by Client or any of its users in the course of utilizing the Platform ("<b>Client Content</b>") that: (1) is unlawful or encourages another to engage in anything unlawful; (2) contains a virus or any other similar programs or software which may damage the operation of Popsixle’s or another’s system; (3) violates the rights of any party or infringes upon the patent, trademark, trade secret, copyright, or other intellectual property right of any party; or, (4) is libelous, defamatory, obscene, invasive of privacy or publicity rights, abusing, harassing, fraudulent, misleading, illegal, threatening or bullying. Client understands and agrees that Popsixle reserves the right to edit, modify or remove content being hosted in the Platform, for reasons including but not limited to violations of the above standards.</span></li>
        </br>
        <li class="s7"><span class="s8">Client and its users may not use the Platform in any way that (i) violates the rights of any party or infringes upon the patent, trademark, trade secret, copyright, or other intellectual property right of any party; or (ii) would be in violation of applicable law or cause damage or harm to Popsixle or any third parties.</span></li>
        </br>
        <li class="s7"><span class="s8">Client shall comply with the export laws and regulations of the United States and other applicable jurisdictions in providing and using the Platform. Without limiting the foregoing, Client warrants and represents that it is not named on any U.S. government list of persons or entities prohibited from receiving exports, and Client shall not use, export or re-export the Platform in violation of any U.S. export embargo, prohibition or restriction. </span></li>
        </ol>
        </br>
      <li class="s7"><span class="s8"><b>OWNERSHIP.</b></span></li>
      </br>
      <ol type="a">
        <li class="s7"><span class="s8">Popsixle retains all right, title and interest in and to the Platform and any and all updates, upgrades or new modules now or hereafter included or made available to Client through the Platform. Title to and ownership of any modifications, suggestions, feedback or customizations of the Platform shall be held exclusively by Popsixle and, to the extent necessary, is hereby assigned by Client to Popsixle. Client agrees to perform such acts, and execute and deliver such instruments and documents, and do all other things as may be reasonably necessary to evidence or perfect the rights of Popsixle set forth in this Section.</span></li>
        </br>
        <li class="s7"><span class="s8">All Client Content is and shall remain the property of Client or the applicable third party; however, in order to use the functionality of the Platform, Client may provide Client Content to Popsixle through the Platform or otherwise, and Popsixle requires a license to such Client Content in order for the Platform to perform its tasks. Client therefore grants to Popsixle, during the Term, and thereafter if Client shares its Client Content with third-parties on the Platform, a nonexclusive, worldwide, royalty-free license to use, reproduce, modify, transmit, display, prepare derivative works from and otherwise utilize the Client Content in and through the Platform as instructed by Client and to facilitate Client’s instructions. </span></li>
        </br>
      </ol>
      <li class="s7"><span class="s8"><b>CONFIDENTIALITY.</b> It is anticipated that the Parties may exchange certain proprietary information necessary to carry out obligations set forth hereunder, which may be collected during the use of the Platform, and which may be otherwise discussed between the Parties. In order for each Party to access, use and track the other Party’s proprietary information, the Parties agree as follows:</span></li>
      </br>
      <ol type="a">
        <li class="s7"><span class="s8"><b>Definition.  “Confidential Information”</b> as used in this Agreement means any and all information disclosed by a Party (each a "<b>Discloser</b>") to the other Party (each a "<b>Recipient</b>"). Notwithstanding the foregoing, as between Popsixle and Client, the Platform is deemed to be Popsixle’s Confidential Information. Confidential Information shall not include information that: (i) was generally known or available at the time it was disclosed or has subsequently become generally known or available through no fault of Recipient; (ii) was rightfully in Recipient’s possession free of any obligation of confidence at or subsequent to the time it was communicated to Recipient by Discloser; or (iii) is independently developed by Recipient without use of Discloser’s Confidential Information as documented by competent records.</span></li>
        </br>
        <li class="s7"><span class="s8"><b>Use Limitations; Nondisclosure Obligation; Duty of Care.</b>  Each Party agrees as a Recipient:  (i) not to use Confidential Information of the other Party for any purpose except in furtherance of its rights or obligations hereunder; (ii) that it shall disclose the other Party’s Confidential Information only to its employees, advisors, contractors or consultants who need to know such information in order to carry out obligations hereunder, and certifies that such individuals have previously agreed, either as a condition to employment or in order to obtain the Confidential Information of the other Party, to be bound by terms and conditions at least as restrictive as those of this Section, provided that any act or omission by such a third party that would be a violation of this Agreement if committed by a Party hereto shall be deemed a breach by the Party that provided the information to such third party; and (iii) to treat all Confidential Information of the other Party with the same degree of care as it accords its own confidential information of a similar nature, but in no case less than with a reasonable degree of care. A breach of these obligations may result in irreparable and continuing damage to the Discloser for which there may be no adequate remedy at law, and, in the event thereof, Discloser shall be entitled to seek injunctive or other equitable relief as may be appropriate. 
</span></li>
        </br>
        <li class="s7"><span class="s8"><b>Required Disclosures.</b> Recipient may disclose Confidential Information of the other Party as and to the extent required by a valid order of a court or other governmental body, as otherwise required by law, or as necessary to establish the rights of either Party under this Agreement. For disclosures required by law, a Party shall first, where permitted by law, notify the other Party of the disclosure and grant such other Party a reasonable opportunity to contest or otherwise limit such disclosure.
</span></li>
        </br>
        <li class="s7"><span class="s8"><b>Data Collection.</b>  Client agrees that Popsixle may, but is not required to, monitor Client’s and its users’ use of the Platform and collect and use data and related information regarding such use which may be gathered periodically to ensure compliance with this Agreement, to facilitate the provision of updates, product support and other services (if any), and for Popsixle’s general business internal purposes. Subject to the foregoing use exceptions, other than in de-identified form, all such data shall be considered Client’s Confidential Information. Information included in a de-identified data set (“Anonymous Data”) shall not be considered Client’s Confidential Information, and Popsixle may use such Anonymous Data for Popsixle’s general business purposes. Client further agrees and acknowledges that the Privacy Policy shall generally govern Popsixle’s use, collection (through the Platform or otherwise), and retention of data.  Client shall comply with all applicable laws with respect to data and Client Content, including, but not limited to, obtaining necessary permissions and consents prior to transferring any such materials to Popsixle and third parties.
</span></li>
        </br>
      </ol>
      <li class="s7"><span class="s8"><b>INDEMNIFICATION.</b> Client will defend, indemnify, and hold Popsixle (and its officers, directors, employees and agents) harmless from and against  all costs, liabilities, losses, and expenses (including reasonable attorneys’ fees) incurred in connection with any third party claim, suit, action, or proceeding (i) alleging that Client Content or other data, information or instructions supplied by Client infringes or caused Popsixle to infringe the intellectual property rights or other rights of a third party or has caused harm to a third party, (ii) arising out of, or relating, to Client’s or its users’ use of the Platform in violation of the law, or (iii) arising out of, or relating to, an actual or alleged breach of this Agreement by Client. </span></li>
      </br>
      <li class="s7"><span class="s8"><b>DISCLAIMER; LIMITATION OF LIABILITY.</b> EXCEPT AS OTHERWISE PROVIDED HEREIN, THE PLATFORM AND ALL COMPONENTS THEREOF ARE PROVIDED “AS IS” AND “AS AVAILABLE”, AND POPSIXLE MAKES NO WARRANTIES OF ANY KIND, INCLUDING, BUT NOT LIMITED TO WITH RESPECT TO THE PLATFORM AND REPORTS, WHETHER EXPRESS, IMPLIED OR STATUTORY, INCLUDING ANY WARRANTIES OF MERCHANTABILITY, QUALITY, NON-INFRINGEMENT, ACCURACY, RESULTS, OR FITNESS FOR A PARTICULAR PURPOSE, ALL OF WHICH ARE EXPRESSLY DISCLAIMED. EXCEPT AS PROHIBITED BY APPLICABLE LAW, OR IN RELATION A PARTY’S DUTIES UNDER SECTIONS 5 OR 6, IN NO EVENT SHALL EITHER PARTY BE LIABLE FOR ANY INCIDENTAL, CONSEQUENTIAL, SPECIAL, OR INDIRECT DAMAGES OF ANY KIND, INCLUDING WITHOUT LIMITATION THOSE RELATING TO LOSS OF USE, LOSS OF DATA, INTERRUPTION OF BUSINESS AND/OR COST OF PROCUREMENT OF SUBSTITUTE GOODS, REGARDLESS OF THE FORM OF ACTION, WHETHER IN CONTRACT, TORT OR OTHERWISE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. EXCEPT FOR LIABILITY RELATED TO INDEMNIFICATION OBLIGATIONS, NEITHER PARTY’S AGGREGATE LIABILITY SHALL EXCEED $500 IN THE AGGREGATE.</span></li>
      </br>
      <li class="s7"><span class="s8"><b>TERM AND TERMINATION.</b> This Agreement shall commence on the Effective Date and remain in force for the length of time identified in the Invoice, unless earlier terminated as provided herein (the “<b>Term</b>”).  Either party may terminate this Agreement immediately if the other party materially breaches the terms hereof and fails to cure such breach within thirty (30) days of its receipt of notice of such breach from the other party.  Further Popsixle may terminate this Agreement for any reason or no reason any time upon notice to Client. In the event this Agreement is terminated for any reason, all rights to access the Platform shall immediately cease. Sections 2-10 will survive any termination or expiration of this Agreement.</span></li>
      </br>
      <li class="s7"><span class="s8"><b>CLIENT REPRESENTATIONS AND WARRANTIES.</b> Client hereby represents and warrants the following: by checking the “I accept” box below and continuing, that Client is either an individual acting solely on its own behalf, or an authorized representative of its employer, or any other entity on whose behalf, or for whose benefit, it uses this Platform, and has the capacity to bind its employer, or such other entity, to these terms and conditions; that it shall not use the Platform for illegal or unauthorized purposes; that no Client Content will violate the rights of a third party or applicable law; and that no additional consents or permissions are required in order for Client to enter into this Agreement and become bound by these terms.</span></li>
      </br>
      <li class="s7"><span class="s8"><b>MISCELLANEOUS.</b> This Agreement constitutes the Parties’ entire understanding and agreement with respect to the subject matter hereof, and supersedes all prior and contemporaneous agreements, representations and understandings between the Parties regarding the subject matter hereof. If any provision of this Agreement is found to be invalid or unenforceable, such provision shall be severed from the Agreement and the remainder of this Agreement shall be interpreted so as best to reasonably affect the intent of the Parties hereto. This Agreement may be amended, and the observance of any term of this Agreement may be waived, only by a writing signed by the Party to be bound. This Agreement shall be governed by the laws of the State of Delaware without application of its conflicts of laws principles. Client may not assign this Agreement, and any assignments shall be null and void. Popsixle may assign or transfer this Agreement at any time.  This Agreement may be executed in one or more counterparts all of which together shall constitute one and the same instrument. Each party agrees that the electronic signatures, whether digital or encrypted, of the parties are intended to authenticate this Agreement and are to have the same force and effect as manual signatures. “Electronic signature” means any electronic sound, symbol or process attached to or logically associated with a record and executed and adopted by a party with the intent to sign such record, including facsimile or e-mail electronic signatures.</span></li>
      </br>

    </ol>

  </div>

</body>
</html>
