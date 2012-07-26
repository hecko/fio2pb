#!/usr/bin/perl

use strict;
use warnings;
use DBI;
use DBD::mysql;
use Time::Local;

my $dbh;
my $prog = "fio2pb";

binmode(STDOUT, ":utf8");

print "Starting...\n";

sub db_reconnect {
	print "(re)connecting DB\n";
	$dbh->disconnect if $dbh;
	my $dsn = "DBI:mysql:fio2pb:localhost";
	$dbh = DBI->connect($dsn,'slsp','slsp')
			or die "$prog FAIL: Unable to connect: $dbh->errstr";
	$dbh->ping or die "$prog FAIL: Unable to verify open database: $dbh->errstr";
}

db_reconnect();

my @files = <data/*>;

#datove pole obsahuje:
#
#  0 Prevod
#  1 Dátum;
#  2 Objem;
#  3 Protiúèet;
#  4 Kód banky;
#  5 KS;
#  6 VS;
#  7 spec Symbol;
#  U<9e>ívate¾ská identifikácia;Typ;Vykonal;Názov protiúètu;Názov banky;

foreach my $file (@files) {
	open(FILE, "< $file");
	my $i = 0;
	foreach my $line (<FILE>) {
		if ($i<10) {
			$i++;
			next;
		}
		chomp($line);
		my @d = split(/;/,$line);
		#parse date string to unix timestamp
		my @t = $d[1] =~ m!(\d{2})\.(\d{2})\.(\d{4})!;
		$t[1]--;
		my $timestamp = timelocal 0,0,12,@t[0,1,2];
		print 	"         Datum: $d[1] ($timestamp)\n".
			"          Suma: $d[2]\n".
			"            Od: $d[8]\n".
			"           Typ: $d[9]\n".
			"          Info: KS:$d[5] VS:$d[6] SS:$d[7] $d[8] $d[10]\n\n";
		my $sth = $dbh->prepare("INSERT INTO `transactions` (`timestamp`,`amount`,`info`) VALUES (?,?,?)")
			or print "$prog FAIL: mysql prepare failed: $dbh->errstr\n";
		$sth->execute($timestamp,$d[2],$d[5]." ".$d[6]." ".$d[7]." ".$d[8]." ".$d[10]) or print "$prog FAIL: mysql exec failed: $dbh->errstr\n";
	}
}

print "Done\n";
