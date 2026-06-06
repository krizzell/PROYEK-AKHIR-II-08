package utils

import (
	"strings"
	"time"
)

const LatePaymentFee = 20000

var monthNames = map[string]time.Month{
	"januari":   time.January,
	"jan":       time.January,
	"februari":  time.February,
	"feb":       time.February,
	"maret":     time.March,
	"mar":       time.March,
	"april":     time.April,
	"apr":       time.April,
	"mei":       time.May,
	"may":       time.May,
	"juni":      time.June,
	"jun":       time.June,
	"juli":      time.July,
	"jul":       time.July,
	"agustus":   time.August,
	"agu":       time.August,
	"aug":       time.August,
	"september": time.September,
	"sep":       time.September,
	"oktober":   time.October,
	"okt":       time.October,
	"oct":       time.October,
	"november":  time.November,
	"nov":       time.November,
	"desember":  time.December,
	"des":       time.December,
	"dec":       time.December,
}

func LateFeeForPeriod(periode string, status string, now time.Time) float64 {
	if strings.EqualFold(status, "lunas") {
		return 0
	}

	month, year, ok := parsePeriod(periode)
	if !ok {
		return 0
	}

	dueDate := time.Date(year, month, 10, 23, 59, 59, 0, now.Location())
	if now.After(dueDate) {
		return LatePaymentFee
	}

	return 0
}

func TotalWithLateFee(baseAmount float64, periode string, status string, now time.Time) float64 {
	return baseAmount + LateFeeForPeriod(periode, status, now)
}

func parsePeriod(periode string) (time.Month, int, bool) {
	clean := strings.ToLower(strings.TrimSpace(periode))
	clean = strings.TrimPrefix(clean, "spp ")
	parts := strings.Fields(clean)

	var month time.Month
	year := 0

	for _, part := range parts {
		if parsedMonth, ok := monthNames[part]; ok {
			month = parsedMonth
			continue
		}

		if len(part) == 4 {
			if parsedYear, err := time.Parse("2006", part); err == nil {
				year = parsedYear.Year()
			}
		}
	}

	if month == 0 || year == 0 {
		return 0, 0, false
	}

	return month, year, true
}
