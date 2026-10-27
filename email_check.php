<?php
// Strict email checks: format, a real domain that can receive mail, no throwaway domains, common typos.
// Returns "" when the email is fine, otherwise a message to show the user.
function validate_email_strict($email) {
    $email = strtolower(trim($email));
    if ($email === "" || strlen($email) > 150) return "Enter a valid email address.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Enter a valid email address, for example name@gmail.com.";

    [$local, $domain] = explode("@", $email, 2);
    $parts = explode(".", $domain);
    $tld = end($parts);
    if (count($parts) < 2 || !preg_match('/^[a-z]{2,24}$/', $tld)) return "That email domain does not look right.";

    // The main part of the domain (the bit before .com / .co.za) must be at least 2 characters, so g.com is refused
    $twoLevel = ["co.za", "org.za", "ac.za", "gov.za", "net.za", "web.za", "co.uk", "org.uk", "ac.uk", "com.au"];
    $last2 = $parts[count($parts) - 2] . "." . $tld;
    $idx = in_array($last2, $twoLevel, true) ? count($parts) - 3 : count($parts) - 2;
    $label = $parts[$idx] ?? "";
    if (strlen($label) < 2) return "That email domain is too short to be real. Use your own email address.";

    // Common typos of popular providers
    $typos = [
        "gmial.com" => "gmail.com", "gmai.com" => "gmail.com", "gamil.com" => "gmail.com", "gnail.com" => "gmail.com",
        "gmal.com" => "gmail.com", "gmail.co" => "gmail.com", "gmail.con" => "gmail.com", "gmail.cm" => "gmail.com",
        "hotmial.com" => "hotmail.com", "hotmal.com" => "hotmail.com", "hotmail.con" => "hotmail.com",
        "outlok.com" => "outlook.com", "outlook.con" => "outlook.com",
        "yahooo.com" => "yahoo.com", "yaho.com" => "yahoo.com", "yahoo.con" => "yahoo.com",
        "iclod.com" => "icloud.com", "icloud.con" => "icloud.com",
    ];
    if (isset($typos[$domain])) return "Did you mean " . $local . "@" . $typos[$domain] . "?";

    // Throwaway email services
    $disposable = ["mailinator.com", "10minutemail.com", "guerrillamail.com", "tempmail.com", "temp-mail.org", "yopmail.com",
        "trashmail.com", "sharklasers.com", "getnada.com", "maildrop.cc", "dispostable.com", "fakeinbox.com", "throwawaymail.com"];
    if (in_array($domain, $disposable, true)) return "Temporary email addresses are not allowed. Use your real email.";

    // Well-known providers: skip the network lookup
    $known = ["gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "live.com", "icloud.com", "msn.com", "proton.me", "protonmail.com",
        "mweb.co.za", "telkomsa.net", "vodamail.co.za", "webmail.co.za", "richfield.ac.za", "yahoo.co.za", "polka.co.za"];
    if (in_array($domain, $known, true)) return "";

    // Real check: the domain must exist and be able to receive mail (MX record, or an A record as a fallback)
    $fqdn = $domain . ".";
    $hasMail = checkdnsrr($fqdn, "MX") || checkdnsrr($fqdn, "A");
    if (!$hasMail) {
        // If DNS is unreachable (no internet), we cannot judge, so let it through rather than block real people
        $dnsWorks = checkdnsrr("gmail.com.", "MX");
        if ($dnsWorks) return "That email domain does not exist or cannot receive email. Check the spelling.";
    }
    return "";
}