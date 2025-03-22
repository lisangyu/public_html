#!/bin/bash

# 检查是否提供了蛋白质序列文件
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <protein_sequence.fasta>"
    exit 1
fi

# 输入蛋白质序列文件
protein_sequence_file=$1

# 设置输出文件
motifs_report="motifs_report.txt"
motifs_plot="motifs_plot.png"

# 使用 patmatmotifs 执行 Prosite 基序扫描
echo "Scanning protein sequence for Prosite motifs..."
patmatmotifs -sequence $protein_sequence_file -prosite -outfile $motifs_report

# 检查扫描是否成功
if [ ! -f $motifs_report ]; then
    echo "Error: Failed to generate motifs report."
    exit 1
fi

echo "Motif scan completed. Report saved to $motifs_report."

# 解析报告并生成 Python 脚本用于图表生成
python3 - << EOF
import matplotlib.pyplot as plt

# 解析 Prosite 基序扫描报告
def parse_motifs_report(file):
    motifs_info = []
    with open(file, 'r') as f:
        lines = f.readlines()
        for line in lines:
            if line.startswith("MOTIF"):
                motif_name = line.split()[0]
                motif_desc = line.split(":")[1].strip()
                motifs_info.append(f"基序名称: {motif_name}, 描述: {motif_desc}")
    return motifs_info

# 生成图表
def generate_motif_plot(motifs_info, output_file):
    motif_names = [motif.split(":")[0] for motif in motifs_info]
    motif_counts = [motif_names.count(motif) for motif in motif_names]

    plt.figure(figsize=(10, 6))
    plt.bar(motif_names, motif_counts, color='skyblue')
    plt.xlabel('基序名称')
    plt.ylabel('出现次数')
    plt.title('Prosite 基序扫描结果')
    plt.xticks(rotation=90)
    plt.tight_layout()
    plt.savefig(output_file)
    print(f"Plot saved to {output_file}")

# 主程序
motifs_info = parse_motifs_report("$motifs_report")
for motif in motifs_info:
    print(motif)

# 生成图表
generate_motif_plot(motifs_info, "$motifs_plot")

EOF

echo "Python script executed. Check the plot at $motifs_plot."

echo "Scan and analysis complete."
